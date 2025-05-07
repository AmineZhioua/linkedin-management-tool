<template>
    <div 
        class="fixed bottom-5 right-2 p-4 bg-white rounded-full flex items-center justify-center"
        style="box-shadow: #00000066 0px 0px 20px 0px;"
    >
        <div v-if="!showNotification && notifications.length > 0" class="absolute py-2 px-3 bg-red-500 top-[-15px] left-[-10px] rounded-full text-white fw-bold">
            {{ notifications.length }}
        </div>
        <button @click="toggleNotification" class="z-50">
            <img 
                src="/build/assets/icons/notification-black.svg" 
                height="30" 
                width="30" 
                alt="Notification Icon"
            />
        </button>
        
        <!-- Notifications List -->
        <div 
            v-if="showNotification"
            class="notification-container absolute max-h-[350px] flex flex-col bottom-[90px] right-[20px] bg-white rounded-xl shadow-lg overflow-y-scroll"
        >
            <div v-if="notifications.length === 0" class="p-4 text-center min-w-[300px] text-gray-500">
                <p class="mb-0">
                    {{ notificationMessage || 'Pas de notifications pour le moment' }}
                </p>
            </div>
            <div 
                v-else
                v-for="notification in notifications"
                :class="{
                    'p-4 min-w-[300px] cursor-pointer hover:bg-gray-200': notification.read_at === null,
                    'p-4 min-w-[300px] cursor-not-allowed bg-gray-200': notification.read_at !== null,
                }" 
                style="border-bottom: 1px solid #ccc;"
            >
                <div class="flex items-center justify-between gap-2" :key="notification.id">
                    <p class="text-black mb-0">{{ notification.message }}</p>

                    <i 
                        v-if="notification.read_at !== null"
                        class="fa-solid fa-circle-check text-xl text-green-500"
                    ></i>

                    <button
                        v-if="notification.read_at === null"
                        @click="markAsRead(notification.id)"
                    >
                        <i class="fa-solid fa-envelope-circle-check text-2xl hover:text-green-500 transition-all duration-200"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'NotificationCard',

    props: {
        userId: {
            type: Number,
            required: true
        }
    },

    data() {
        return {
            notifications: [],
            showNotification: false,
            notificationMessage: '',
            readDateTime: '',
        }
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
                    this.notificationMessage = response.data.message || 'Pas de Notifications pour le moment';
                } else if (response.data.status === 201) {
                    this.notifications = response.data.data;
                    this.notificationMessage = '';
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
                this.notifications = [];
                this.notificationMessage = 'Une erreur s\'est produite lors de la récupération des notifications';
            }
        },

        formatDateTime(date) {
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${date.toISOString().split('T')[0]}T${hours}:${minutes}`;
        },

        async markAsRead(notificationId) {
            try {
                const markAsReadData = new FormData();

                markAsReadData.append("notification_id", notificationId);
                markAsReadData.append('_method', 'PUT');

                const response = await axios.post('/linkedin/mark-as-read', markAsReadData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });

                if (response.data.success === true) {
                    // this.notifications = this.notifications.filter(n => n.id !== notificationId);
                    
                    // If all notifications are read, update message
                    if (this.notifications.length === 0) {
                        this.notificationMessage = 'Pas de notifications pour le moment';
                    }
                    
                    console.log("Notification marked as read successfully");
                } else {
                    console.error("Failed to mark notification as read:", response.data.message);
                }
            } catch (error) {
                console.error("Request error:", error);
                // You might want to show an error message to the user
            }
        },

        toggleNotification() {
            this.showNotification = !this.showNotification;
        },

        // Notification Listener for Campaign Notifications
        async listenToCampaignNotifications() {
            window.Echo.private(`campaign-started.${this.userId}`).listen('.CampaignStarted', async (event) => {
                const notification = {
                    user_id: event.user_id,
                    linkedin_user_id: event.linkedin_user_id,
                    campaign_id: event.campaign_id,
                    event_name: event.event_name,
                    message: event.message
                };
                await this.addNotification(notification);
                this.notifications.push(notification);
                console.log('Campaign Started notification added:', notification);
            });

            window.Echo.private(`campaign-completed.${this.userId}`).listen('.CampaignCompleted', async (event) => {
                const notification = {
                    user_id: event.user_id,
                    linkedin_user_id: event.linkedin_user_id,
                    campaign_id: event.campaign_id,
                    event_name: event.event_name,
                    message: event.message
                };
                await this.addNotification(notification);
                this.notifications.push(notification);
                console.log('Campaign Completed notification added:', notification);
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
                    message: event.message
                };
                await this.addNotification(notification);
                this.notifications.push(notification);
                console.log('Post notification added:', notification);
            });

            window.Echo.private(`post-failed.${this.userId}`).listen('.PostFailed', async (event) => {
                const notification = {
                    user_id: event.user_id,
                    linkedin_user_id: event.linkedin_user_id,
                    campaign_id: event.campaign_id,
                    event_name: event.event_name,
                    message: event.message
                };
                await this.addNotification(notification);
                this.notifications.push(notification);
                console.log('Post notification added:', notification);
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
        }
    }
}
</script>

<style scoped>
.notification-container::-webkit-scrollbar {
    width: 6px;
}

.notification-container::-webkit-scrollbar-track {
    background: white;
}

.notification-container::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, rgb(255 16 185) 0%, rgb(255 125 82) 100%);
    border-radius: 20px;
}
</style>