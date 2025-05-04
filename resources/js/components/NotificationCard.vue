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

        <div v-if="showNotification" v-for="notification in notifications">
            <div class="absolute top-[-190%] min-w-[300px] right-0 bg-white p-4 rounded shadow-lg">
                <h2 class="text-lg font-bold">Notification</h2>
                <p class="text-gray-700">{{ notification }}</p>
            </div>
        </div>
    </div>
</template>

<script>
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
        console.log("#########################");
        this.listenToPostsNotifications();
    },

    methods: {
        toggleNotification() {
            this.showNotification = !this.showNotification;
        },

        listenToCampaignNotifications() {
            console.log('Setting up Echo listener from mounted');
            window.Echo.private(`campaign-started.${this.userId}`).listen('.CampaignStarted', (event) => {
                // alert(`Message received: ${event.message}`);
                this.notifications.push(event.message);
                console.log('Notifications array:', this.notifications);
                console.log(event);
            });
            console.log('Echo listener set up from mounted');
        },

        listenToPostsNotifications() {
            console.log('Setting up Echo listener from mounted for posts');
            window.Echo.private(`post-posted.${this.userId}`).listen('.PostPosted', (event) => {
                // alert(`Message received About the post: ${event.message}`);
                this.notifications.push(event.message);
                console.log('Notifications array about posts:', this.notifications);
                console.log(event);
            });
            console.log('Echo listener set up from mounted for posts');
        }
    }
}
</script>

<style scoped>

</style>