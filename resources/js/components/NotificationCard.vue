<template>
    <div 
        class="fixed bottom-5 right-0 p-4 bg-white rounded-full flex items-center justify-center"
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

        <div v-if="showNotification" v-for="notification in notifications" :key="notification.message">
            <div class="absolute top-[-190%] min-w-[300px] right-0 bg-white p-4 rounded shadow-lg">
                <h2 class="text-lg font-bold">Notification</h2>
                <p class="text-gray-700">{{ notification.message }}</p>
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

        async listenToCampaignNotifications() {
            // console.log('Setting up Echo listener from mounted');
            window.Echo.private(`campaign-started.${this.userId}`).listen('.CampaignStarted', (event) => {
                const notification = {
                    user_id: event.user_id,
                    linkedin_user_id: event.linkedin_user_id,
                    campaign_id: event.campaign_id,
                    event_name: event.event_name,
                    message: event.message
                };
                this.notifications.push(notification);
                console.log('Campaign notification added:', notification);
            });
            // console.log('Echo listener set up from mounted');
        },

        listenToPostsNotifications() {
            // console.log('Setting up Echo listener from mounted for posts');
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
            // console.log('Echo listener set up from mounted for posts');
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
</style>