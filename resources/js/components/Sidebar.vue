<template>
    <div class="bg-black min-h-full w-[300px]" style="border-top: 1px solid gray;">
        <ul class="list-style-none pl-2 flex flex-col gap-4 py-4 mr-2">
            <li 
                class="flex items-center gap-2 text-white cursor-pointer hover:bg-pink-300 py-2 px-2 rounded-lg transition-all duration-300"
                @click="cardsNavigation('dashboard')"    
            >
                <img 
                    src="/build/assets/icons/dashboard-white.svg" 
                    alt="Dashboard Icon" 
                    height="25" 
                    width="25"
                />
                <span class="text-md fw-semibold">Dashboard</span>
            </li>
            <li 
                class="flex items-center gap-2 text-white cursor-pointer hover:bg-pink-300 py-2 px-2 rounded-lg transition-all duration-300"
                @click="cardsNavigation('comptes')"
            >
                <img 
                    src="/build/assets/icons/linkedin.svg" 
                    alt="Dashboard Icon" 
                    height="25" 
                    width="25"
                />
                <span class="text-md fw-semibold">Vos Comptes</span>
            </li>
            <li 
                class="flex items-center gap-2 text-white cursor-pointer hover:bg-pink-300 py-2 px-2 rounded-lg transition-all duration-300"
                @click="cardsNavigation('userPosts')"
            >
                <img 
                    src="/build/assets/icons/posts-white.svg" 
                    alt="Dashboard Icon" 
                    height="25" 
                    width="25"
                />
                <span class="text-md fw-semibold">Vos Posts</span>
            </li>
            <li 
                class="flex items-center gap-2 text-white cursor-pointer hover:bg-pink-300 py-2 px-2 rounded-lg transition-all duration-300"
                @click="cardsNavigation('calendar')"
            >
                <img 
                    src="/build/assets/icons/calendar-white.svg" 
                    alt="Dashboard Icon" 
                    height="25" 
                    width="25"
                />
                <span class="text-md fw-semibold">Calendrier</span>
            </li>
            <li 
                class="flex items-center gap-2 text-white cursor-pointer hover:bg-pink-300 py-2 px-2 rounded-lg transition-all duration-300"
                @click="cardsNavigation('KPIs')"    
            >
                <img 
                    src="/build/assets/icons/kpi-white.svg" 
                    alt="Dashboard Icon" 
                    height="25" 
                    width="25"
                />
                <span class="text-md fw-semibold">Vos Statistiques</span>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: 'Sidebar',

    emits: ['card-to-set'],

    data() {
        return {
            card: "userPosts", // GONNA CHANGE THIS TO DASHBOARD AS SOON AS EVERYTHING IS IN PLACE
        };
    },

    methods: {
        cardsNavigation(card) {
            this.card = card;
            this.$emit('card-to-set', this.card);
        },
    },
};
</script>