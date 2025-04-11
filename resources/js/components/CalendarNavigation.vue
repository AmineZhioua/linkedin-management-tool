<template>
    <div class="flex justify-between items-center mb-4">
        <button 
            @click="prevMonth" 
            class="bg-gray-200 hover:bg-gray-300 py-2 px-4 rounded-lg"
        >
            &larr; Mois précédent
        </button>
        
        <h3 class="text-lg font-semibold month">{{ formatMonth() }}</h3>
        
        <button 
            @click="nextMonth" 
            class="bg-gray-200 hover:bg-gray-300 py-2 px-4 rounded-lg"
        >
            Mois suivant &rarr;
        </button>
    </div>
</template>


<script>
export default {
    name: "CalendarNavigation",
    props: {
        currentMonth: Number,   
        currentYear: Number,
    },
    
    emits: ['update:currentMonth', 'update:currentYear'],

    methods: {
        prevMonth() {
            let newMonth = this.currentMonth;
            let newYear = this.currentYear;
            
            if (newMonth === 0) {
                newMonth = 11;
                newYear--;
            } else {
                newMonth--;
            }
            
            this.$emit('update:currentMonth', newMonth);
            this.$emit('update:currentYear', newYear);
        },
        
        nextMonth() {
            let newMonth = this.currentMonth;
            let newYear = this.currentYear;
            
            if (newMonth === 11) {
                newMonth = 0;
                newYear++;
            } else {
                newMonth++;
            }
            
            this.$emit('update:currentMonth', newMonth);
            this.$emit('update:currentYear', newYear);
        },
        
        formatMonth() {
            const date = new Date(this.currentYear, this.currentMonth, 1);
            return date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
        },
    }
}
</script>