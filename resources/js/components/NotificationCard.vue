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
            <div 
                v-for="notification in notifications"
                class="p-4 min-w-[300px] cursor-pointer hover:bg-gray-200" style="border-bottom: 1px solid #ccc;"
            >
                <div class="flex items-center justify-between gap-2">
                    <p class="text-black mb-0">{{ notification.message }}</p>
                    <button>
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
        }
    },

    mounted() {
        this.listenToCampaignNotifications();
        this.listenToPostsNotifications();
    },

    watch: {
        'notifications.length': {
            handler(newLength, oldLength) {
                if (newLength > oldLength) {
                    const newNotifications = this.notifications.slice(oldLength);
                    this.addNotificationToDatabase(newNotifications);
                }
            }
        }
    },

    methods: {
        toggleNotification() {
            this.showNotification = !this.showNotification;
        },

        listenToCampaignNotifications() {
            window.Echo.private(`campaign-started.${this.userId}`).listen('.CampaignStarted', (event) => {
                const notification = {
                    user_id: event.user_id,
                    linkedin_user_id: event.linkedin_user_id,
                    campaign_id: event.campaign_id,
                    event_name: event.event_name,
                    message: event.message
                };
                this.notifications.push(notification);
                console.log('Campaign Started notification added:', notification);
            });

            window.Echo.private(`campaign-completed.${this.userId}`).listen('.CampaignCompleted', (event) => {
                const notification = {
                    user_id: event.user_id,
                    linkedin_user_id: event.linkedin_user_id,
                    campaign_id: event.campaign_id,
                    event_name: event.event_name,
                    message: event.message
                };
                this.notifications.push(notification);
                console.log('Campaign Completed notification added:', notification);
            });
        },

        listenToPostsNotifications() {
            window.Echo.private(`post-posted.${this.userId}`).listen('.PostPosted', (event) => {
                const notification = {
                    user_id: event.user_id,
                    linkedin_user_id: event.linkedin_user_id,
                    campaign_id: event.campaign_id,
                    event_name: event.event_name,
                    message: event.message
                };
                this.notifications.push(notification);
                console.log('Post notification added:', notification);
            });

            window.Echo.private(`post-failed.${this.userId}`).listen('.PostFailed', (event) => {
                const notification = {
                    user_id: event.user_id,
                    linkedin_user_id: event.linkedin_user_id,
                    campaign_id: event.campaign_id,
                    event_name: event.event_name,
                    message: event.message
                };
                console.log("this is from the post failed event");
                this.notifications.push(notification);
                console.log('Post notification added:', notification);
            });
        },

        async addNotificationToDatabase(newNotifications) {
            try {
                const notificationsData = newNotifications.map(notification => ({
                    user_id: notification.user_id,
                    linkedin_user_id: notification.linkedin_user_id,
                    campaign_id: notification.campaign_id,
                    event_name: notification.event_name,
                    message: notification.message
                }));

                const response = await axios.post('/add-notification', { notifications: notificationsData }, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });

                if (response.status === 201) {
                    console.log("Notifications added successfully:", response.data.message);
                }
            } catch (error) {
                console.error('Error adding notifications:', error.response ? error.response.data : error.message);
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