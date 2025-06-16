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
      <li 
        class="flex relative items-center gap-2 text-white cursor-pointer hover:bg-pink-300 py-2 px-2 rounded-lg transition-all duration-300"
        @click="cardsNavigation('commhub')"    
      >
      <svg xmlns="http://www.w3.org/2000/svg" height="27px" viewBox="0 -960 960 960" width="27px" fill="#FFFFFF">
        <path d="M0-240v-63q0-43 44-70t116-27q13 0 25 .5t23 2.5q-14 21-21 44t-7 48v65H0Zm240 0v-65q0-32 17.5-58.5T307-410q32-20 76.5-30t96.5-10q53 0 97.5 10t76.5 30q32 20 49 46.5t17 58.5v65H240Zm540 0v-65q0-26-6.5-49T754-397q11-2 22.5-2.5t23.5-.5q72 0 116 26.5t44 70.5v63H780Zm-455-80h311q-10-20-55.5-35T480-370q-55 0-100.5 15T325-320ZM160-440q-33 0-56.5-23.5T80-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T160-440Zm640 0q-33 0-56.5-23.5T720-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T800-440Zm-320-40q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-600q0 50-34.5 85T480-480Zm0-80q17 0 28.5-11.5T520-600q0-17-11.5-28.5T480-640q-17 0-28.5 11.5T440-600q0 17 11.5 28.5T480-560Zm1 240Zm-1-280Z"/>
      </svg>
        <p>CommHub</p>
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