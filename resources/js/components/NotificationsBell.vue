<template>
    <div class="notification-container relative z-50">
        <button class="relative" @click="toggleNotifications">
            <i class="fa-regular fa-bell text-white p-2 rounded-full notification-icon" style="font-size: 27px;"></i>
            <p class="mb-0 absolute bg-red-500 text-white w-7 h-7 flex items-center justify-center top-[-7px] right-[-7px] rounded-full">
                {{ notificationsNumber }}
            </p>
        </button>

        <!-- Notifications List -->
        <div 
            v-if="showNotifications"
            class="bg-white rounded-md absolute top-[50px] right-0 shadow-lg overflow-y-scroll max-h-[350px] flex flex-col"
        >
            <div 
                v-for="notification in notifications"
                class="py-4 px-2 min-w-[300px] cursor-pointer hover:bg-gray-200 border-t-black border-t-2 flex flex-col gap-2"
            >
                <!-- Badge for to display if something FAILS -->
                <div 
                    v-if="notification.event_name === 'PostFailed'"
                    class="bg-red-500 rounded-xl py-1 px-2 w-fit"
                >
                    <p class="mb-0 text-white text-xs">Echéc</p>
                </div>
                <!-- Badge for to display if something SUCCEED -->
                <div 
                    v-if="notification.event_name === 'PostPosted' || notification.event_name === 'CampaignStarted' || notification.event_name === 'CampaignCompleted'"
                    class="bg-green-500 rounded-xl py-1 px-2 w-fit"
                >
                    <p class="mb-0 text-white text-xs">{{ successMessageBadge(notification) }}</p>
                </div>
                <!-- Notification Message -->
                <div class="flex items-center gap-1 px-1">
                    <p class="text-black mb-0 ml-2">{{ notification.message }}</p>
                    <img src="/build/assets/icons/mark-read.svg" alt="Mark Icon" />
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import axios from 'axios';
import { useToast } from "vue-toastification";


export default {
    name: 'NotificationsBell',

    props: {
        userId: {
            type: Number,
            required: true
        }
    },

    data() {
        return {
            showNotifications: false,
            notifications: [],
            notificationsNumber: 0,
            notificationMessage: '',
        };
    },

    setup() {
        const toast = useToast();
        return { toast }
    },

    mounted() {
        this.listenToCampaignNotifications();
        this.listenToPostsNotifications();
        this.getNotifications();
    },

    methods: {
        async getNotifications() {
            try {
                const response = await axios.get('/get-notifications', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });

                if (response.data.status === 200) {
                    this.notifications = [];
                    this.notificationsNumber = 0;
                    this.notificationMessage = response.data.message || 'Pas de Notifications pour le moment';
                } else if (response.data.status === 201) {
                    this.notifications = response.data.data;
                    this.notificationsNumber = response.data.unread_count;
                    this.notificationMessage = '';
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
                this.notifications = [];
                this.notificationMessage = 'Une erreur s\'est produite lors de la récupération des notifications';
            }
        },

        // Notification Listener for Campaign Notifications
        async listenToCampaignNotifications() {
            window.Echo.private(`campaign-started.${this.userId}`).listen('.CampaignStarted', async (event) => {
                const notification = {
                    user_id: event.user_id,
                    linkedin_user_id: event.linkedin_user_id,
                    campaign_id: event.campaign_id,
                    event_name: event.event_name,
                    message: event.message,
                    read_at: null,
                };

                await this.addNotification(notification);
                this.notifications.unshift(notification);
                this.notificationsNumber += 1;
                this.showToast();
            });

            window.Echo.private(`campaign-completed.${this.userId}`).listen('.CampaignCompleted', async (event) => {
                const notification = {
                    user_id: event.user_id,
                    linkedin_user_id: event.linkedin_user_id,
                    campaign_id: event.campaign_id,
                    event_name: event.event_name,
                    message: event.message,
                    read_at: null,
                };

                await this.addNotification(notification);
                this.notifications.unshift(notification);
                this.notificationsNumber += 1;
                this.showToast();
            });
        },

        // Notification Listener for Posts Notifications
        async listenToPostsNotifications() {
            window.Echo.private(`post-posted.${this.userId}`).listen('.PostPosted', async (event) => {
                const notification = {
                    user_id: event.user_id,
                    linkedin_user_id: event.linkedin_user_id,
                    campaign_id: event.campaign_id,
                    event_name: event.event_name,
                    message: event.message,
                    read_at: null,
                };

                await this.addNotification(notification);
                this.notifications.unshift(notification);
                this.notificationsNumber += 1;
                this.showToast();
            });

            window.Echo.private(`post-failed.${this.userId}`).listen('.PostFailed', async (event) => {
                const notification = {
                    user_id: event.user_id,
                    linkedin_user_id: event.linkedin_user_id,
                    campaign_id: event.campaign_id,
                    event_name: event.event_name,
                    message: event.message,
                    read_at: null,
                };

                await this.addNotification(notification);
                this.notifications.unshift(notification);
                this.notificationsNumber += 1;
                this.showToast();
            });
        },

        // Function to Add Notifications to Database
        async addNotification(notification) {
            try {
                const notificationData = new FormData();

                notificationData.append("user_id", notification.user_id);
                notificationData.append("linkedin_user_id", notification.linkedin_user_id);
                notificationData.append("campaign_id", notification.campaign_id);
                notificationData.append("event_name", notification.event_name);
                notificationData.append("message", notification.message);

                const response = await axios.post('/notifications', notificationData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });

                if (response.status === 201) {
                    console.log("Notification added successfully:", response.data.message);
                }
            } catch (error) {
                console.error('Error adding notification:', error.response ? error.response.data : error.message);
            }
        },

        showToast() {
            this.toast.info("Vous avez une nouvelle Notification", {
                position: "bottom-right",
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: false,
                timeout: 2500,
                closeButton: "button",
                icon: true,
                rtl: false
            });
        },

        async markAllAsRead() {
            try {
                const markResponse = await axios.post('/linkedin/mark-all-as-read', {}, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });

                if (markResponse.data.status === 200) {
                    this.notificationsNumber = 0;
                    this.notifications.forEach(notif => notif.read_at = new Date().toISOString());
                }

            } catch(error) {
                console.error('Error marking notifications as read:', error);
            }
        },

        successMessageBadge(notification) {
            if(notification.event_name === 'PostPosted') {
                return 'Succès';
            } else if(notification.event_name === 'CampaignStarted') {
                return 'Début';
            } else if(notification.event_name === 'CampaignCompleted') {
                return 'Complété';
            }

            return 'Erreur lors de l`\'affichage de notification';
        },

        toggleNotifications() {
            this.showNotifications = !this.showNotifications;
            if (this.showNotifications) {
                this.markAllAsRead();
            }
        },
    },
};
</script>

<style scoped>
.notification-icon:hover {
    background-color: rgba(128, 128, 128, 0.733);
}
</style>