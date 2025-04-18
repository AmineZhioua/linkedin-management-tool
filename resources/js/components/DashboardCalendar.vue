<template>
    <div class="dashboard-calendar">
        <calendar-navigation v-model:currentMonth="currentMonth" v-model:currentYear="currentYear" />

        <!-- Day Headers -->
        <div class="grid grid-cols-7 text-center font-medium mb-2">
            <div class="py-2">Sun</div>
            <div class="py-2">Mon</div>
            <div class="py-2">Tue</div>
            <div class="py-2">Wed</div>
            <div class="py-2">Thu</div>
            <div class="py-2">Fri</div>
            <div class="py-2">Sat</div>
        </div>
    
        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-1">
            <!-- Blank days before the first day of the month -->
            <template v-for="blankDay in Array.from({ length: getFirstDayOfMonth(currentMonth, currentYear) }, (_, i) => i)" :key="'blank-' + blankDay">
                <div class="h-24 bg-gray-100 rounded"></div>
            </template>
    
            <!-- Days of the month -->
            <template v-for="day in getDaysInMonth(currentMonth, currentYear)" :key="day">
                <div 
                    class="h-24 border rounded overflow-hidden relative p-1"
                    :class="{
                    'bg-red-700': displayCampaigns(day),
                    'cursor-pointer': getPostsForDate(day).length > 0,
                    'cursor-not-allowed': getPostsForDate(day).length === 0,
                    }"
                    @click="getPostsForDate(day).length > 0 ? showPostsPopover(day) : null"
                >
                <!-- 'bg-gray-50': !isDayInCampaign(day), -->
                <!-- 'bg-yellow-50': isDayInCampaign(day), -->

                    <div class="text-right text-sm">{{ day }}</div>
                    <div class="overflow-y-auto" style="max-height: 80px;">
                        <div 
                            v-for="post in getPostsForDate(day)"
                            class="text-xs p-1 mb-1 rounded truncate"
                            :key="post.id || post.tempId"
                            :class="{
                            'bg-green-200': post.type === 'text',
                            'bg-yellow-200': post.type === 'image',
                            'bg-red-200': post.type === 'video',
                            'bg-purple-300': post.type === 'article'
                            }"
                        >
                            {{ getPostTypeIcon(post.type) }} {{ formatTime(post.scheduledDateTime) }}
                        </div>
                    </div>
                </div>
            </template>
        </div>
  
        <!-- Popover for viewing posts -->
        <div v-if="showPopover" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closePopover">
            <div class="bg-white shadow-lg flex flex-col rounded-lg p-4 z-10 max-w-md w-full min-h-[400px]" @click.stop>
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-lg">Posts for {{ selectedDay }} {{ getMonthName(currentMonth) }} {{ currentYear }}</h3>
                    <button 
                        class="text-black text-3xl"
                        @click="closePopover"
                    >
                        ×
                    </button>
                </div>
                <div class="mt-4 flex-1">
                    <div v-if="getPostsForDate(selectedDay).length === 0" class="text-center text-gray-500 py-4">
                    No posts scheduled
                    </div>
                    <div v-else class="space-y-2">
                        <div 
                            v-for="post in getPostsForDate(selectedDay)"
                            :key="post.id || post.tempId"
                            class="p-3 rounded flex items-center justify-between"
                            :class="{
                            'bg-green-200': post.type === 'text',
                            'bg-yellow-200': post.type === 'image',
                            'bg-red-200': post.type === 'video',
                            'bg-purple-200': post.type === 'article'
                            }"
                        >
                            <div class="flex items-center w-full">
                                <div class="flex-1"> 
                                    <span class="mr-2">{{ getPostTypeIcon(post.type) }}</span>
                                    <span>{{ formatTime(post.scheduledDateTime) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
  
<script>
export default {
  name: 'DashboardCalendar',
  props: {
    campaigns: {
      type: Array,
      required: true
    },
    posts: {
      type: Array,
      required: true
    },
    initialMonth: {
      type: Number,
      required: true
    },
    initialYear: {
      type: Number,
      required: true
    }
  },
  
    data() {
        console.log(this.posts);
        console.log(this.campaigns);
        return {
            selectedDay: null,
            showPopover: false,
            currentMonth: this.initialMonth,
            currentYear: this.initialYear,
        };
    },
  
    methods: {
        displayCampaigns(day) {
            // Check if any campaign includes this day
            return this.campaigns.some(campaign => {
                const startDate = new Date(campaign.start_date);
                const endDate = new Date(campaign.end_date);
                
                // Create a date object for the day we're checking
                const checkDate = new Date(this.currentYear, this.currentMonth, day);
                
                // Simply check if the date falls within the campaign period
                return checkDate >= startDate && checkDate <= endDate;
            });
        },

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
                    postDate.getMonth() === this.currentMonth && 
                    postDate.getFullYear() === this.currentYear;
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
            this.showPopover = true;
        },
        
        closePopover() {
            this.showPopover = false;
        },
        
        getMonthName(monthIndex) {
            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            return months[monthIndex];
        },
    
        isDayInCampaign(day) {
            const currentDate = new Date(this.currentYear, this.currentMonth, day);
            return this.campaigns.some(campaign => {
                const start = new Date(campaign.start_date);
                const end = new Date(campaign.end_date);
                return currentDate >= start && currentDate <= end;
            });
        },
    }
}
</script>
  
<style scoped>
.dashboard-calendar {
  max-width: 100%;
}
</style>