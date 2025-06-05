<template>
  <div class="bg-black min-h-full" style="border-top: 1px solid gray;">
    <ul class="list-style-none pl-2 flex flex-col gap-4 py-4 mr-2">
      <li 
        class="flex relative items-center gap-2 text-white cursor-pointer hover:bg-pink-300 py-2 px-2 rounded-lg transition-all duration-300"
        @click="cardsNavigation('dashboard')"    
      >
        <img 
          src="/build/assets/icons/dashboard-white.svg" 
          alt="Dashboard Icon" 
          height="25" 
          width="25"
        />

        <p>Dashboard</p>
      </li>
      <li 
        class="flex relative items-center gap-2 text-white cursor-pointer hover:bg-pink-300 py-2 px-2 rounded-lg transition-all duration-300"
        @click="cardsNavigation('linkedinAccounts')"
      >
        <img 
          src="/build/assets/icons/linkedin.svg" 
          alt="LinkedIn Icon" 
          height="25" 
          width="25"
        />
        <p>Espace LinkedIn</p>
      </li>
      <li 
        class="flex relative items-center gap-2 text-white cursor-pointer hover:bg-pink-300 py-2 px-2 rounded-lg transition-all duration-300"
        @click="cardsNavigation('userPosts')"
      >
        <img 
          src="/build/assets/icons/posts-white.svg" 
          alt="Posts Icon" 
          height="25" 
          width="25"
        />
        <p>Activités</p>
      </li>
      <li 
        class="flex relative items-center gap-2 text-white cursor-pointer hover:bg-pink-300 py-2 px-2 rounded-lg transition-all duration-300"
        @click="cardsNavigation('calendar')"
      >
        <img 
          src="/build/assets/icons/calendar-white.svg" 
          alt="Calendar Icon" 
          height="25" 
          width="25"
        />
        <p>Calendrier</p>
      </li>
      <li 
        class="flex relative items-center gap-2 text-white cursor-pointer hover:bg-pink-300 py-2 px-2 rounded-lg transition-all duration-300"
        @click="cardsNavigation('KPIs')"    
      >
        <img 
          src="/build/assets/icons/kpi-white.svg" 
          alt="KPI Icon" 
          height="25" 
          width="25"
        />
        <p>Statistiques</p>
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
      card: 'KPIs',
    };
  },
  methods: {
    cardsNavigation(card) {
      this.card = card;
      this.$emit('card-to-set', card);
    },
  },
};
</script>


<style scoped>
ul li p {
  position: absolute;
  top: 50%;
  left: 120%;
  transform: translateY(-50%);
  color: white;
  font-weight: 600;
  padding: 8px 12px;
  background-color: oklch(82.3% 0.12 346.018);
  z-index: 50;
  border-radius: 15px;
  box-shadow: 0 0 7px 3px #00000057;
  display: none;
}

ul li p::before {
    content: "";
    position: absolute;
    border-width: 10px;
    border-style: solid;
    border-color: transparent oklch(82.3% 0.12 346.018) transparent transparent;
    left: -15px;
    top: 50%;
    transform: translateY(-50%);
}

ul li:hover p {
  display: block;
}
</style>