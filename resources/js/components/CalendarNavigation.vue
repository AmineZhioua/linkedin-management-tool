<template>
    <div class="flex justify-between items-center mb-4">
        <!-- Navigation Buttons -->
        <div class="flex items-center gap-4">
            <button 
                @click="$emit('navigate', 'prev')" 
                class="bg-black hover:bg-gray-300 py-2 px-3 rounded-full"
            >
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                    <path d="M560-240 320-480l240-240 56 56-184 184 184 184-56 56Z"/>
                </svg>
            </button>
            
            <h3 class="text-lg mb-0 font-semibold month">{{ formatMonth() }}</h3>
            
            <button 
                @click="$emit('navigate', 'next')" 
                class="bg-black hover:bg-gray-300 py-2 px-3 rounded-full"
            >
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                    <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                </svg>
            </button>
        </div>

        <!-- Switch Between Week & Month View -->
        <div class="border py-2 px-2 rounded flex items-center gap-2">
            <button 
                class="py-3 px-4 bg-red-500 fw-semibold rounded-lg"
                :class="viewMode === 'week' ? 'btn-active' : 'btn-inactive'"
                @click="setViewMode('week')"
            >
                Semaine
            </button>

            <button 
                class="py-3 px-4 bg-red-500 rounded-lg fw-semibold"
                :class="viewMode === 'month' ? 'btn-active' : 'btn-inactive'"
                @click="setViewMode('month')"
            >
                Mois
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "CalendarNavigation",
    props: {
        currentMonth: Number,   
        currentYear: Number,
        viewMode: String,
    },

    emits: ['navigate', 'update:viewMode'],

    methods: {
        setViewMode(mode) {
            this.$emit('update:viewMode', mode);
        },

        formatMonth() {
            const date = new Date(this.currentYear, this.currentMonth, 1);
            return date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
        },
    }
}
</script>

<style scoped>
.btn-active {
    background: linear-gradient(
        135deg,
        rgb(255 16 185) 0%,
        rgb(255 125 82) 100%
    );
    font-weight: 600;
    box-shadow: 0 2px 5px rgba(79, 70, 229, 0.3);
    transition: all 0.3s ease;
    font-size: 0.9rem;
    color: white;
}
.btn-inactive {
    color: #6b7280;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    background-color: white;
    color: black;
}
.btn-inactive:hover {
    background: rgba(255, 16, 183, 0.236);
    color: white;
}
</style>