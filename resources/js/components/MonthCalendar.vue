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
                    class="h-24 border rounded overflow-hidden relative p-1"
                    :class="{'bg-blue-50 hover:bg-blue-300 cursor-pointer': getPostsForDate(day).length > 0}"
                    @click="showPostsPopover(day)"
                >
                    <div class="text-right text-xs">{{ day }}</div>
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
                        <!-- @click.stop="onEditPost(post)" -->
                            {{ getPostTypeIcon(post.type) }} {{ formatTime(post.scheduledDateTime) }}
                        </div>
                    </div>
                </div>
            </template>

            <!-- Posts List Element Popover -->
            <div v-if="showPopover" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closePopover">
                <div class="bg-white shadow-lg rounded-lg p-4 z-10 max-w-md w-full" @click.stop>
                    <div class="flex justify-between align-items-center">
                        <h3 class="font-semibold text-lg">Posts du {{ selectedDay }} {{ getMonthName(month) }} {{ year }}</h3>
                        <button 
                            class="text-black text-3xl"
                            @click="closePopover"
                        >
                            &times;
                        </button>
                    </div>
                    <div class="mt-4">
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
                                <div>
                                    <span class="mr-2">{{ getPostTypeIcon(post.type) }}</span>
                                    <span>{{ formatTime(post.scheduledDateTime) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button 
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                            @click="closePopover"
                        >
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'MonthCalendar',
    props: {
        posts: Array,
        month: Number,
        year: Number,
        onEditPost: Function,
    },
    
    data() {
        return {
            selectedDay: null,
            showPopover: false
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
            if (this.getPostsForDate(day).length > 0) {
                this.selectedDay = day;
                this.showPopover = true;
            }
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
    }
}
</script>