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
                    class="h-24 border rounded overflow-hidden relative p-1 cursor-pointer hover:bg-gray-50"
                    :class="{'bg-blue-50': getPostsForDate(day).length > 0}"
                >
                    <div class="text-right text-xs">{{ day }}</div>
                    <div class="overflow-y-auto" style="max-height: 80px;">
                        <div 
                            v-for="post in getPostsForDate(day)"
                            class="text-xs p-1 mb-1 rounded truncate cursor-pointer hover:bg-blue-100"
                            :class="{
                                'bg-green-100': post.type === 'text',
                                'bg-yellow-100': post.type === 'image',
                                'bg-red-100': post.type === 'video',
                                'bg-purple-100': post.type === 'article'
                            }"
                            @click.stop="onEditPost(post)"
                        >
                            {{ getPostTypeIcon(post.type) }} {{ formatTime(post.scheduledDateTime) }}
                        </div>
                    </div>
                </div>
            </template>
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
    }
}
</script>


<style scoped>

</style>