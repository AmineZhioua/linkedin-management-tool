<template>
    <div class="calendar-month">
        <div class="grid grid-cols-7 text-center font-medium mb-2">
            <div class="py-2">Dim</div>
            <div class="py-2">Lun</div>
            <div class="py-2">Mar</div>
            <div class="py-2">Mer</div>
            <div class="py-2">Jeu</div>
            <div class="py-2">Ven</div>
            <div class="py-2">Sam</div>
        </div>
        <div class="grid grid-cols-7 gap-1">
            <template v-for="blankDay in getFirstDayOfMonth(month, year)">
                <div class="h-24 bg-gray-100 rounded"></div>
            </template>
            <template v-for="day in getDaysInMonth(month, year)">
                <div 
                    class="h-32 border rounded overflow-hidden relative p-1"
                    :class="{
                        'hover:opacity-70 cursor-pointer': getPostsForDate(day).length > 0,
                        [campaignColor]: getPostsForDate(day).length > 0
                    }"
                    :style="getPostsForDate(day).length > 0 ? { backgroundColor: campaignColor } : {}"
                    @click="showPostsPopover(day)"
                >
                    <div class="text-right text-sm">{{ day }}</div>
                    <div class="overflow-y-auto" style="max-height: 80px;">
                        <div 
                            v-for="post in getPostsForDate(day)"
                            class="text-xs p-1 mb-1 rounded truncate cursor-pointer"
                            :key="post.id || post.tempId"
                            :class="{
                                'bg-green-200 hover:bg-green-400': post.type === 'text',
                                'bg-yellow-200 hover:bg-yellow-400': post.type === 'image',
                                'bg-red-200 hover:bg-red-400': post.type === 'video',
                                'bg-purple-300 hover:bg-purple-400': post.type === 'article'
                            }"
                            @click="showPostsPopover(day)"
                        >
                            {{ getPostTypeIcon(post.type) }} {{ formatTime(post.scheduledDateTime) }}
                        </div>
                    </div>
                </div>
            </template>

            <!-- Posts List Element Popover -->
            <div v-if="showPopover" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closePopover">
                <div class="bg-white shadow-lg flex flex-col rounded-lg p-4 z-10 max-w-md w-full min-h-[400px]" @click.stop>
                    <div class="flex justify-between align-items-center">
                        <h3 class="font-semibold text-lg">Posts du {{ selectedDay }} {{ getMonthName(month) }} {{ year }}</h3>
                        <button 
                            class="text-black text-3xl"
                            @click="closePopover"
                        >
                            ×
                        </button>
                    </div>
                    <div class="mt-4 flex-1">
                        <div v-if="getPostsForDate(selectedDay).length === 0" class="text-center text-gray-500 py-4">
                            Aucun post prévu
                        </div>
                        <div v-else class="space-y-2">
                            <div 
                                v-for="post in getPostsForDate(selectedDay)"
                                :key="post.id || post.tempId"
                                class="p-3 rounded flex items-center justify-between cursor-pointer"
                                :class="{
                                    'bg-green-200 hover:bg-green-300': post.type === 'text',
                                    'bg-yellow-200 hover:bg-yellow-300': post.type === 'image',
                                    'bg-red-200 hover:bg-red-300': post.type === 'video',
                                    'bg-purple-200 hover:bg-purple-300': post.type === 'article'
                                }"
                                @click="onEditPost(post)"
                            >
                                <div class="flex align-items-center w-full">
                                    <div class="flex-1"> 
                                        <span class="mr-2">{{ getPostTypeIcon(post.type) }}</span>
                                        <span>{{ formatTime(post.scheduledDateTime) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button 
                            class="bg-blue-500 flex gap-2 text-white px-4 py-2 rounded hover:bg-blue-600 w-fit"
                            @click="addPostForDay"
                        >
                            <img 
                                src="/build/assets/icons/add-white.svg" 
                                alt="Add Icon"
                            />
                            <span>Ajouter un post</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Error Messages -->
        <Popup
            v-if="showErrorPopup"
            :key="popupKey"
            path="/build/assets/popups/sad-face.svg"
            @close="showErrorPopup = false"
        >
            {{ errorMessage }}
        </Popup>
    </div>
</template>

<script>
import Popup from './Popup.vue';

export default {
    name: 'MonthCalendar',

    components: {
        Popup,
    },

    props: {
        posts: Array,
        month: Number,
        year: Number,
        onEditPost: Function,
        campaignStartDateTime: String,
        campaignEndDateTime: String,
        campaignColor: String,
    },
    
    data() {
        return {
            selectedDay: null,
            showPopover: false,
            showErrorPopup: false,
            errorMessage: '',
            popupKey: 0, // Added to force re-rendering of Popup
        };
    },

    methods: {
        getDaysInMonth(month, year) {
            return new Date(year, month + 1, 0).getDate();
        },

        getFirstDayOfMonth(month, year) {
            return new Date(year, month, 1).getDay();
        },

        getPostsForDate(date) {
            return this.posts.filter(post => {
                const postDate = new Date(post.scheduledDateTime);
                return postDate.getDate() === date && 
                        postDate.getMonth() === this.month && 
                        postDate.getFullYear() === this.year;
            });
        },

        formatTime(dateTime) {
            return new Date(dateTime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        getPostTypeIcon(type) {
            switch(type) {
                case 'text': return '📝';
                case 'image': return '🖼️';
                case 'video': return '🎬';
                case 'article': return '📰';
                default: return '📌';
            }
        },
        
        showPostsPopover(day) {
            this.selectedDay = day;

            const campaignStart = new Date(this.campaignStartDateTime);
            const campaignEnd = new Date(this.campaignEndDateTime);
            const selectedDate = new Date(this.year, this.month, day);

            if (isNaN(campaignStart.getTime()) || isNaN(campaignEnd.getTime())) {
                console.error('Invalid campaign date range provided');
                return;
            }

            const campaignStartDate = new Date(campaignStart);
            campaignStartDate.setHours(0, 0, 0, 0);

            const campaignEndDate = new Date(campaignEnd);
            campaignEndDate.setHours(0, 0, 0, 0);

            const normalizedSelectedDate = new Date(selectedDate);
            normalizedSelectedDate.setHours(0, 0, 0, 0);

            if (normalizedSelectedDate < campaignStartDate || normalizedSelectedDate > campaignEndDate) {
                this.errorMessage = 'La date sélectionnée est en dehors de la plage de la campagne.';
                this.popupKey++; // Increment key to force Popup re-render
                this.showErrorPopup = true;
                return;
            }

            this.showErrorPopup = false;
            this.showPopover = true;
        },
        
        closePopover() {
            this.showPopover = false;
        },
        
        getMonthName(monthIndex) {
            const months = [
                'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
            ];
            return months[monthIndex];
        },

        addPostForDay() {
            this.$emit('add-post', this.selectedDay);
        },
    }
}
</script>

<style>
/* ::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: transparent;
  border-radius: 20px;
} */
</style>