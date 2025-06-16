<template>
    <div class="w-full h-full p-4 overflow-y-scroll">
        <!-- Title & Paragraph -->
        <div class="flex items-center justify-between">
            <div class="flex flex-col">
                <div class="flex items-center gap-2 text-black">
                    <img 
                        src="/build/assets/icons/kpi-black.svg" 
                        alt="KPI Icon" 
                        class="mb-1"
                        height="50"
                        width="50"
                    />
                    <h1 class="mb-0 fw-semibold">Vos Statistiques</h1>
                </div>
                <p class="mt-2 text-lg text-muted">
                    Vue d'ensemble détaillée de vos performances, 
                    basée sur des métriques essentielles pour piloter efficacement votre activité !
                </p>
            </div>
            
            <button class="flex items-center gap-2 bg-black rounded-lg text-white py-2 px-3 text-xl">
                <i class="fa-solid fa-robot"></i>
                <p class="mb-0 mt-1">Recommendation AI</p>
            </button>
        </div>

        <!-- CARDS SECTION -->
        <div class="grid grid-cols-3 gap-4 mt-4">
            <!-- TOTAL POSTS CARD -->
            <div class="flex justify-between border shadow-md p-4 rounded-lg" style="background-color: #18181b;">
                <div class="flex flex-col gap-2">
                    <h1 class="text-white text-xl">Posts</h1>
                    <h1 class="mb-0 text-white">= {{ allLinkedinPosts.length }}</h1>
                    <p class="mb-0 text-gray-300 text-sm">Nombre des posts depuis la création de votre compte</p>
                </div>
                <div class="py-2 px-3 text-center rounded-xl h-fit flex items-center justify-center" style="background-color: #8280ff;">
                    <i class="fa-solid fa-paper-plane text-3xl text-purple-1000"></i>
                </div>
            </div>

            <!-- AVERAGE POSTS PER DAY CARD -->
            <div class="flex justify-between border shadow-md p-4 rounded-lg" style="background-color: #18181b;">
                <div class="flex justify-between flex-col gap-2">
                    <h1 class="text-white text-xl">Moyenne Posts / Jour</h1>
                    <h1 class="mb-0 text-white">= {{ calculateAllTimeAveragePostsPerDay() }}</h1>
                    <p class="mb-0 text-gray-300 text-sm">Moyenne des posts par jour depuis la création de votre compte</p>
                </div>
                <div class="py-2 px-3 text-center rounded-xl h-fit flex items-center justify-center" style="background-color: #ffc53d;">
                    <i class="fa-solid fa-calendar-check text-3xl text-purple-1000"></i>
                </div>
            </div>

            <!-- BEST POST TYPE CARD -->
            <div class="flex justify-between border gap-2 shadow-md p-4 rounded-lg" style="background-color: #18181b;">
                <div class="flex flex-col gap-2">
                    <h1 class="text-white text-xl">Type de post ultime</h1>
                    <!-- Loading state for the best engagement value -->
                    <h3 v-if="isLoading" class="text-white text-xl mb-2 fw-light">Chargement...</h3>
                    <!-- The value itself -->
                    <h1 v-else class="mb-2 text-white text-3xl fw-light">{{ this.bestEngagementRatePostType }}</h1>

                    <p class="mb-0 text-gray-300 text-sm">Le type du post qui la meilleure performance par rapport aux autres</p>
                </div>
                <div class="py-2 px-3 text-center rounded-xl h-fit flex items-center justify-center" style="background-color: #da4dd0;">
                    <i class="fa-solid fa-image text-3xl text-pink-1000"></i>
                </div>
            </div>
        </div>

        <div class="flex gap-2 w-full my-4 shadow-md" style="border-bottom: 1px solid rgb(0, 0, 0, 0.5);">
            <div 
                class="p-4 flex items-center gap-1 justify-center cursor-pointer" 
                style="border-top-left-radius: 5px; border-top-right-radius: 5px;"
                @click="setCurrentSection('posts')"
                :class="currentSection === 'posts' ? 'active-section' : 'opacity-70'"
            >
                <i class="fa-solid fa-images text-xl"></i>
                <h3 class="mb-0 text-xl fw-semibold">Posts</h3>
            </div>
            <div 
                class="p-4 flex items-center gap-1 justify-center cursor-pointer" 
                style="border-top-left-radius: 5px; border-top-right-radius: 5px;"
                @click="setCurrentSection('campaigns')"
                :class="currentSection === 'campaigns' ? 'active-section' : 'opacity-70'"
            >
                <i class="fa-solid fa-bullhorn text-xl"></i>
                <h3 class="mb-0 text-xl fw-semibold">Campagnes</h3>
            </div>
        </div>

        <!-- POSTS STATS SECTION -->
        <template v-if="currentSection === 'posts'">
            <div class="flex flex-col gap-3 mt-4">
                <div class="flex items-center justify-between">
                    <h3 class="fw-semibold text-2xl">Distribution des interactions sur les Posts :</h3>
                    <div class="relative">
                        <button 
                            @click="chooseAccountDropdown = !chooseAccountDropdown"
                            class="flex items-center gap-2 p-1 border rounded-xl text-white"
                            style="background-color: #18181b;"
                        >
                            <div class="flex items-center p-2">
                                <div v-if="chosenAccount" class="flex items-center gap-2">
                                    <img 
                                        :src="chosenAccount.linkedin_picture ?? '/build/assets/images/default-profile.png'" 
                                        alt="Profile Picture"
                                        height="40"
                                        width="40" 
                                        class="rounded-full"
                                    />
                                    <span>{{ chosenAccount.linkedin_firstname }} {{ chosenAccount.linkedin_lastname }}</span>
                                </div>
                                <span v-else>Tous les comptes</span>
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                                    <path d="M480-360 280-560h400L480-360Z"/>
                                </svg>
                            </div>
                        </button>
                        <ul 
                            v-if="chooseAccountDropdown"
                            class="absolute right-0 shadow-lg rounded-md px-0 z-50 text-white"
                            style="background-color: #18181b;"
                        >
                            <li 
                                class="flex justify-center gap-2 px-3 py-2 hover:bg-slate-500 rounded-md cursor-pointer"
                                @click="selectAccount(null)"
                            >
                                <p class="mb-0">Tous les Comptes</p>
                            </li>
                            <li 
                                v-for="linkedinAccount in allLinkedinAccounts"
                                class="flex items-center gap-2 px-3 py-2 hover:bg-slate-500 rounded-md cursor-pointer"
                                @click="selectAccount(linkedinAccount)"
                            >
                                <img 
                                    :src="linkedinAccount.linkedin_picture ?? '/build/assets/images/default-profile.png'" 
                                    alt="Profile Picture"
                                    height="40"
                                    width="40" 
                                    class="rounded-full"
                                />
                                <span class="line-clamp-1">
                                    {{ linkedinAccount.linkedin_firstname }} {{ linkedinAccount.linkedin_lastname }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- ALL ENGAGEMENT CHARTS FOR THE SOCIAL ACTIONS (LIKES & COMMENTS) -->
                <div class="grid grid-cols-4 gap-3">
                    <!-- Text Posts Engagement Chart -->
                    <div class="chart-container p-2 pt-4 rounded-xl shadow-lg" style="position: relative; height: 400px; background-color: #18181b;">
                        <canvas id="textEngagementChart"></canvas>
                        <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                        <div v-if="errorMsg && !isLoading" class="text-center text-danger mt-2">{{ errorMsg }}</div>
                    </div>
                    
                    <!-- Image Posts Engagement Chart -->
                    <div class="chart-container p-2 pt-4 rounded-xl shadow-lg" style="position: relative; height: 400px; background-color: #18181b;">
                        <canvas id="imageEngagementChart"></canvas>
                        <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Video Posts Engagement Chart -->
                    <div class="chart-container p-2 pt-4 rounded-xl shadow-lg" style="position: relative; height: 400px; background-color: #18181b;">
                        <canvas id="videoEngagementChart"></canvas>
                        <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Article Posts Engagement Chart -->
                    <div class="chart-container p-2 pt-4 rounded-xl shadow-lg" style="position: relative; height: 400px; background-color: #18181b;">
                        <canvas id="articleEngagementChart"></canvas>
                        <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Taux d'Engagement par type Cards Section -->
                <div class="flex flex-col gap-2 mt-1">
                    <!-- Title & Icon -->
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="42px" viewBox="0 -960 960 960" width="42px" fill="#000000">
                            <path d="M80-480v-80h120v80H80Zm136 222-56-58 84-84 58 56-86 86Zm28-382-84-84 56-58 86 86-58 56Zm476 480L530-350l-50 150-120-400 400 120-148 52 188 188-80 80ZM400-720v-120h80v120h-80Zm236 80-58-56 86-86 56 56-84 86Z"/>
                        </svg>
                        <h1 class="mb-0 fw-semibold text-2xl">Taux d'Engagement :</h1>
                    </div>

                    <div class="grid grid-cols-5 gap-4">
                        <div 
                            v-for="type of typesForEngagementRate"
                            class="flex justify-between border shadow-md p-4 rounded-lg" 
                            style="background-color: #18181b;"
                        >
                            <div class="flex flex-col gap-2">
                                <h1 class="text-white text-xl">{{ type.name }}</h1>
                                <!-- Loading State -->
                                <h3 v-if="isLoading" class="text-white text-xl mb-2 fw-light">Chargement...</h3>

                                <h1 v-else class="mb-0 text-white text-3xl">= {{ this[type.label] }}</h1>
                                <p class="mb-0 text-gray-300 text-sm">Taux d'engagement sur les posts de type {{ type.name }}.</p>
                            </div>
                            <div class="py-2 px-3 text-center rounded-xl h-fit flex items-center justify-center" style="background-color: #8280ff;">
                                <i :class="type.icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col mt-4 gap-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000">
                            <path d="M120-240q-33 0-56.5-23.5T40-320q0-33 23.5-56.5T120-400h10.5q4.5 0 9.5 2l182-182q-2-5-2-9.5V-600q0-33 23.5-56.5T400-680q33 0 56.5 23.5T480-600q0 2-2 20l102 102q5-2 9.5-2h21q4.5 0 9.5 2l142-142q-2-5-2-9.5V-640q0-33 23.5-56.5T840-720q33 0 56.5 23.5T920-640q0 33-23.5 56.5T840-560h-10.5q-4.5 0-9.5-2L678-420q2 5 2 9.5v10.5q0 33-23.5 56.5T600-320q-33 0-56.5-23.5T520-400v-10.5q0-4.5 2-9.5L420-522q-5 2-9.5 2H400q-2 0-20-2L198-340q2 5 2 9.5v10.5q0 33-23.5 56.5T120-240Z"/>
                        </svg>
                        <h3 class="fw-semibold text-2xl mb-0">Engagement par Période :</h3>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- SELECT PERIOD ELEMENT -->
                        <select name="datetime-engagement" 
                            id="datetime-engagement" 
                            class="p-2 text-black bg-white rounded-lg border"
                            v-model="selectedDateRange"                        
                        >
                            <option value="3">Dernier 3 Jours</option>
                            <option value="14">Dernier 14 Jours</option>
                            <option value="30">Dernier 30 Jours</option>
                            <option value="90">Dernier 3 Mois</option>
                            <option value="all">Tous les Temps</option>
                        </select>
                        <!-- SELECT ACCOUNT FOR PERIOD FETCHING -->
                        <div class="relative">
                            <button 
                                @click="engagementPerPeriodAccountDropdown = !engagementPerPeriodAccountDropdown"
                                class="flex items-center gap-2 p-1 border rounded-xl text-white"
                                style="background-color: #18181b;"
                            >
                                <div class="flex items-center p-2">
                                    <div v-if="selectedAccountForEngagementPerPeriod" class="flex items-center gap-2">
                                        <img 
                                            :src="selectedAccountForEngagementPerPeriod.linkedin_picture ?? '/build/assets/images/default-profile.png'" 
                                            alt="Profile Picture"
                                            height="40"
                                            width="40" 
                                            class="rounded-full"
                                        />
                                        <span>
                                            {{ selectedAccountForEngagementPerPeriod.linkedin_firstname }} 
                                            {{ selectedAccountForEngagementPerPeriod.linkedin_lastname }}
                                        </span>
                                    </div>
                                    <span v-else>Tous les Comptes</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                                        <path d="M480-360 280-560h400L480-360Z"/>
                                    </svg>
                                </div>
                            </button>
                            <ul 
                                v-if="engagementPerPeriodAccountDropdown"
                                class="absolute right-0 shadow-lg rounded-md px-0 z-50 text-white"
                                style="background-color: #18181b;"
                            >
                                <li 
                                    class="flex justify-center gap-2 px-3 py-2 hover:bg-slate-500 rounded-md cursor-pointer"
                                    @click="selectAccountForEngagementPerPeriod(null)"
                                >
                                    <p class="mb-0">Tous les Comptes</p>
                                </li>
                                <li 
                                    v-for="linkedinAccount in allLinkedinAccounts"
                                    class="flex items-center gap-2 px-3 py-2 hover:bg-slate-500 rounded-md cursor-pointer"
                                    @click="selectAccountForEngagementPerPeriod(linkedinAccount)"
                                >
                                    <img 
                                        :src="linkedinAccount.linkedin_picture ?? '/build/assets/images/default-profile.png'" 
                                        alt="Profile Picture"
                                        height="40"
                                        width="40" 
                                        class="rounded-full"
                                    />
                                    <span class="line-clamp-1">
                                        {{ linkedinAccount.linkedin_firstname }} {{ linkedinAccount.linkedin_lastname }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- ENGAGEMENT PER PERIOD CHART -->
                <div class="chart-container p-2 pt-4 rounded-lg shadow-lg flex-1" style="position: relative; height: 450px; background-color: #18181b;">
                    <canvas id="engagementPerPeriodChart" style="height: 450px;"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-lg flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                    <div v-if="noData && !isLoading" class="absolute inset-0 flex flex-col gap-3 items-center justify-center text-white text-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#BB271A">
                            <path d="m336-280 144-144 144 144 56-56-144-144 144-144-56-56-144 144-144-144-56 56 144 144-144 144 56 56ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>
                        </svg>
                        <p class="mb-0 fw-light">Pas de données pour ce compte</p>
                    </div>
                    <div v-if="engagementPeriodError && !isLoading" class="text-center text-danger mt-2">{{ engagementPeriodError }}</div>
                </div>
            </div>

            <!-- POSTS DISTRIBUTION BAR & DOUGHNUT CHARTS SECTIONS -->
            <div class="mt-6 flex items-center gap-3">
                <div class="chart-container p-2 pt-4 rounded-lg shadow-lg flex-1" style="position: relative; height: 450px; background-color: #18181b;">
                    <div v-if="isLoading" class="absolute inset-0 rounded-lg flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                    <canvas id="postTypeDistributionChart"></canvas>
                </div>
                <div class="chart-container p-2 pt-4 rounded-lg shadow-lg" style="position: relative; height: 450px; background-color: #18181b;">
                    <div v-if="isLoading" class="absolute inset-0 rounded-lg flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                    <canvas id="postPuslishSuccesRate" class="mx-2"></canvas>
                </div>
            </div>

            <!-- TOP 3 POSTS SECTION -->
            <div class="mt-4 relative p-4">
                <!-- Heading -->
                <div class="flex items-end gap-3">
                    <i class="fa-solid fa-ranking-star text-5xl"></i>
                    <h2 class="mb-0 fw-semibold text-2xl">Meilleurs Posts :</h2>
                </div>

                <div 
                    v-if="isTopPostsLoading" 
                    class="absolute h-[200px] w-full mt-8 inset-0 top-1/2 rounded-xl flex items-center justify-center"
                >
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>

                <div v-else class="grid grid-cols-3 gap-4 mt-4">
                    <div class="order-2 flex flex-col items-center">
                        <linkedin-post
                            v-if="topPerformantPosts[0]"
                            :user-linkedin-accounts="this.allLinkedinAccounts"
                            :post="topPerformantPosts[0].post"
                            :key="topPerformantPosts[0].post.id"
                        />
                    </div>
                    
                    <div class="order-1 translate-y-20 mb-4">
                        <linkedin-post
                            v-if="topPerformantPosts[0]"
                            :user-linkedin-accounts="this.allLinkedinAccounts"
                            :post="topPerformantPosts[0].post"
                            :key="topPerformantPosts[0].post.id"
                        />
                    </div>
                    
                    <div class="order-3 translate-y-20">
                        <linkedin-post
                            v-if="topPerformantPosts[0]"
                            :user-linkedin-accounts="this.allLinkedinAccounts"
                            :post="topPerformantPosts[0].post"
                            :key="topPerformantPosts[0].post.id"
                        />
                    </div>
                </div>
            </div>
        </template>

        <!-- CAMPAIGNS STATS SECTION -->
        <div 
            class="mt-8" 
            v-if="currentSection === 'campaigns'" 
        >
            <!-- Title & Icon -->
            <div class="flex items-center gap-2 justify-between">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" height="42px" viewBox="0 -960 960 960" width="42px" fill="#000000">
                        <path d="M720-440v-80h160v80H720Zm48 280-128-96 48-64 128 96-48 64Zm-80-480-48-64 128-96 48 64-128 96ZM200-200v-160h-40q-33 0-56.5-23.5T80-440v-80q0-33 23.5-56.5T160-600h160l200-120v480L320-360h-40v160h-80Zm240-182v-196l-98 58H160v80h182l98 58Zm120 36v-268q27 24 43.5 58.5T620-480q0 41-16.5 75.5T560-346ZM300-480Z"/>
                    </svg>
                    <h1 class="mb-0 fw-semibold text-2xl">Détails des Campagnes :</h1>
                </div>
                <div class="relative">
                    <button 
                        @click="campaignsSectionDropdown = !campaignsSectionDropdown"
                        class="flex items-center gap-2 p-1 border rounded-xl text-white"
                        style="background-color: #18181b;"
                    >
                        <div class="flex items-center p-2">
                            <div v-if="selectedAccountForCampaigns" class="flex items-center gap-2">
                                <img 
                                    :src="selectedAccountForCampaigns.linkedin_picture ?? '/build/assets/images/default-profile.png'" 
                                    alt="Profile Picture"
                                    height="40"
                                    width="40" 
                                    class="rounded-full"
                                />
                                <span>{{ selectedAccountForCampaigns.linkedin_firstname }} {{ selectedAccountForCampaigns.linkedin_lastname }}</span>
                            </div>
                            <span v-else>Tous les comptes</span>
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff">
                                <path d="M480-360 280-560h400L480-360Z"/>
                            </svg>
                        </div>
                    </button>
                    <ul 
                        v-if="campaignsSectionDropdown"
                        class="absolute right-0 shadow-lg rounded-md px-0 z-50 text-white"
                        style="background-color: #18181b;"
                    >
                        <li 
                            class="flex justify-center gap-2 px-3 py-2 hover:bg-slate-500 rounded-md cursor-pointer"
                            @click="selectAccountForCampaigns(null)"
                        >
                            <p class="mb-0">Tous les Comptes</p>
                        </li>
                        <li 
                            v-for="linkedinAccount in allLinkedinAccounts"
                            class="flex items-center gap-2 px-3 py-2 hover:bg-slate-500 rounded-md cursor-pointer"
                            @click="selectAccountForCampaigns(linkedinAccount)"
                        >
                            <img 
                                :src="linkedinAccount.linkedin_picture ?? '/build/assets/images/default-profile.png'" 
                                alt="Profile Picture"
                                height="40"
                                width="40" 
                                class="rounded-full"
                            />
                            <span class="line-clamp-1">
                                {{ linkedinAccount.linkedin_firstname }} {{ linkedinAccount.linkedin_lastname }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Campaigns Social Actions Chart -->
            <div class="mt-6">
                <div class="chart-container p-2 pt-4 rounded-lg shadow-lg" style="position: relative; height: 450px; background-color: #18181b;">
                    <canvas id="campaignSocialActionsChart"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaigns Engagement Rate Chart -->
            <div class="mt-6">
                <!-- Title & Icon -->
                <div class="flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" height="42px" viewBox="0 -960 960 960" width="42px" fill="#000000">
                            <path d="M80-480v-80h120v80H80Zm136 222-56-58 84-84 58 56-86 86Zm28-382-84-84 56-58 86 86-58 56Zm476 480L530-350l-50 150-120-400 400 120-148 52 188 188-80 80ZM400-720v-120h80v120h-80Zm236 80-58-56 86-86 56 56-84 86Z"/>
                    </svg>
                    <h1 class="mb-0 fw-semibold text-2xl">Taux d'Engagement des Campagnes :</h1>
                </div>
                <div class="chart-container p-2 pt-4 rounded-lg shadow-lg" style="position: relative; height: 450px; background-color: #18181b;">
                    <canvas id="campaignEngagementRateChart"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-3">
                <!-- Post Volume by Campaign HORIZONTAL Chart -->
                <div class="chart-container px-4 p-2 pt-4 rounded-lg shadow-lg flex-1" style="position: relative; height: 450px; background-color: #18181b;">
                    <canvas id="postVolumeByCampaignChart"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
                <!-- Campaign Status Distribution DOUGHNUT Chart -->
                <div class="chart-container p-2 pt-4 rounded-lg shadow-lg" style="position: relative; height: 450px; background-color: #18181b;">
                    <canvas id="campaignStatusDistributionChart" class="mx-2"></canvas>
                    <div v-if="isLoading" class="absolute inset-0 rounded-xl flex items-center justify-center bg-opacity-70" style="background-color: #18181b;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Chart } from 'chart.js/auto';

export default {
    name: 'KpiSection',
    props: {
        allLinkedinAccounts: { 
            type: Array, 
            required: true 
        },
        allLinkedinPosts: { 
            type: Array, 
            required: true 
        },
        allCampaigns: { 
            type: Array, 
            required: true 
        },
    },

    data() {
        return {
            socialActionsData: {},
            topActiveAccount: null,
            totalLikes: 0,
            totalComments: 0,
            accountErrorMessage: '',
            errorMsg: '',
            selectedWeekStart: null,
            selectedWeekEnd: null,
            chooseAccountDropdown: false,
            chosenAccount: null,
            engagementPerPeriodAccountDropdown: false,
            selectedAccountForEngagementPerPeriod: null,
            selectedDateRange: 'all',
            engagementPeriodTotalLikes: 0,
            engagementPeriodTotalComments: 0,
            engagementPeriodError: '',
            engagementByDate: {},
            engagementPerPeriodChartInstance: null,
            noData: false,
            barChartInstance: null,
            doughnutSuccessRateChartInstance: null,
            postVolumeByCampaignChartInstance: null,
            campaignStatusDistributionChart: null,
            textEngagementChartInstance: null,
            imageEngagementChartInstance: null,
            videoEngagementChartInstance: null,
            articleEngagementChartInstance: null,
            campaignSocialActionsChartInstance: null,
            campaignEngagementRateChartInstance: null,
            isSocialActionByPeriodLoading: false,
            isLoading: false,
            // Text posts variables
            totalTextPosts: 0,
            totalLikesTextPosts: 0,
            totalCommentsTextPosts: 0,
            engagementRateTextPosts: 0,
            // Image posts variables
            totalImagePosts: 0,
            totalLikesImagePosts: 0,
            totalCommentsImagePosts: 0,
            engagementRateImagePosts: 0,
            // Video posts variables
            totalVideoPosts: 0,
            totalLikesVideoPosts: 0,
            totalCommentsVideoPosts: 0,
            engagementRateVideoPosts: 0,
            // Article posts variables
            totalArticlePosts: 0,
            totalLikesArticlePosts: 0,
            totalCommentsArticlePosts: 0,
            engagementRateArticlePosts: 0,
            // Multi-image posts variables
            totalMultiimagePosts: 0,
            totalLikesMultiimagePosts: 0,
            totalCommentsMultiimagePosts: 0,
            engagementRateMultiimagePosts: 0,
            // Engagement Rate variables
            isBestEngagementChartLoading: false,
            bestEngagementRate: 0,
            bestEngagementRatePostType: '',
            // Camppaigns variables
            campaignLikes: {},
            campaignComments: {},
            selectedAccountForCampaigns: null,
            campaignsSectionDropdown: false,
            typeErrors: { text: '', image: '', video: '', article: '', multiimage: '' },
            currentSection: 'posts',
            topPostsError: '',
            topPerformantPosts: [],
            isTopPostsLoading: false,
            typesForEngagementRate: [
                { name: 'Image', icon: 'fa-solid fa-image text-2xl', label: 'engagementRateImagePosts'},
                { name: 'Vidéo', icon: 'fa-solid fa-video text-2xl', label: 'engagementRateVideoPosts'},
                { name: 'Multi Image', icon: 'fa-solid fa-images text-2xl', label: 'engagementRateMultiimagePosts'},
                { name: 'Text', icon: 'fa-solid fa-quote-left text-2xl', label: 'engagementRateTextPosts'},
                { name: 'Article', icon: 'fa-solid fa-newspaper text-2xl', label: 'engagementRateArticlePosts'},
            ],
        };
    },

    computed: {
        postTypeDistributionData() {
            const types = ['text', 'image', 'video', 'article', 'multiimage'];
            const typeCounts = types.reduce((acc, type) => ({ ...acc, [type]: 0 }), {});
            this.allLinkedinPosts.forEach(post => {
                if (post.type) typeCounts[post.type]++;
            });
            return typeCounts;
        },

        postStatusDistributionData() {
            const allStatus = ['posted', 'failed', 'queued', 'draft'];
            const statusCounts = allStatus.reduce((acc, type) => ({ ...acc, [type]: 0 }), {});
            this.allLinkedinPosts.forEach(post => {
                if (post.status) statusCounts[post.status]++;
            });
            return statusCounts;
        },

        postVolumeByCampaign() {
            const campaignIds = Object.keys(this.campaignLikes);
            const campaigns = this.allCampaigns.filter(campaign => campaignIds.includes(campaign.id.toString()));
            const postCountsByCampaign = {};
            this.allLinkedinPosts.forEach(post => {
                if (post.campaign_id && campaignIds.includes(post.campaign_id.toString())) {
                    postCountsByCampaign[post.campaign_id] = (postCountsByCampaign[post.campaign_id] || 0) + 1;
                }
            });
            const labels = campaigns.map(campaign => campaign.name);
            const data = campaigns.map(campaign => postCountsByCampaign[campaign.id] || 0);
            return {
                labels,
                datasets: [{
                    label: 'Posts per Campaign',
                    data,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };
        },

        campaignStatusDistributionData() {
            const allStatus = ['scheduled', 'failed', 'completed', 'draft'];
            const statusCounts = allStatus.reduce((acc, status) => ({ ...acc, [status]: 0 }), {});
            const campaigns = this.selectedAccountForCampaigns
                ? this.allCampaigns.filter(campaign => campaign.linkedin_user_id === this.selectedAccountForCampaigns.id)
                : this.allCampaigns;
            campaigns.forEach(campaign => {
                if (campaign.status) statusCounts[campaign.status]++;
            });
            return statusCounts;
        },
    },

    async mounted() {
        await this.fetchAllSocialActions();
        this.calculateAllKPIs();
        this.calculateCampaignsKPIs();
        this.calculateTopPerformantPosts();
        this.$nextTick(() => this.renderCharts());
    },

    watch: {
        allLinkedinPosts: { 
            handler() { 
                this.renderCharts(); 
            }, 
            deep: true 
        },
        allCampaigns: { 
            handler() { 
                this.renderCharts(); 
            }, 
            deep: true 
        },

        selectedDateRange() {
            this.calculateEngagementByPeriod();
            this.noData = false;
        },
        selectedAccountForEngagementPerPeriod() {
            this.calculateEngagementByPeriod();
        },
    },

    methods: {
        async fetchAllSocialActions() {
            this.isLoading = true;
            this.isTopPostsLoading = true;
            const publishedPosts = this.allLinkedinPosts.filter(post => post.status === 'posted');
            
            const promises = publishedPosts.map(post => {
                const linkedinUser = this.getUserLinkedinInfo(post.linkedin_user_id);
                if (!linkedinUser) {
                    console.error(`No LinkedIn user found for linkedin_user_id: ${post.linkedin_user_id}`);
                    return Promise.resolve(null); // Skip posts with no user
                }
                return axios.get('/linkedin/get-social-actions', {
                    params: {
                        post_id: post.id,
                        urn: encodeURIComponent(post.post_urn),
                        linkedin_user_id: post.linkedin_user_id,
                        linkedin_token: linkedinUser.linkedin_token
                    },
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => {
                    if (response.status === 200) {
                        return {
                            postId: post.id,
                            likes: response.data.likesSummary?.totalLikes || 0,
                            comments: response.data.commentsSummary?.aggregatedTotalComments || 0
                        };
                    }
                    console.warn(`Skipping post ${post.id} due to unsuccessful response: ${response.status}`);
                    return null;
                }).catch(error => {
                    if (error.response) {
                        const status = error.response.status;
                        if (status === 401) {
                            console.warn(`Skipping post ${post.id}: Token expired or invalid`);
                        } else if (status === 403) {
                            console.warn(`Skipping post ${post.id}: Insufficient permissions`);
                        } else if (status === 404) {
                            console.warn(`Skipping post ${post.id}: Post not found, possibly deleted`);
                        } else {
                            console.error(`Skipping post ${post.id}: Error status ${status}`, error.response.data);
                        }
                    } else {
                        console.error(`Skipping post ${post.id}: Network or server error`, error);
                    }
                    return null;
                });
            });

            const results = await Promise.all(promises);
            this.socialActionsData = results
                .filter(result => result !== null)
                .reduce((acc, { postId, likes, comments }) => {
                    acc[postId] = { likes, comments };
                    return acc;
                }, {});
            this.isLoading = false;
            this.isTopPostsLoading = false;
        },

        calculateAllKPIs() {
            this.isLoading = true;
            this.isBestEngagementChartLoading = true;
            const types = ['text', 'image', 'video', 'article', 'multiimage'];
            types.forEach(type => {
                const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
                this[`total${capitalizedType}Posts`] = 0;
                this[`totalLikes${capitalizedType}Posts`] = 0;
                this[`totalComments${capitalizedType}Posts`] = 0;
                this.typeErrors[type] = '';
            });

            const postedPosts = this.filterPostsByStatus(this.chosenAccount);
            postedPosts.forEach(post => {
                const type = post.type;
                if (type && this.socialActionsData[post.id]) {
                    const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
                    this[`total${capitalizedType}Posts`]++;
                    this[`totalLikes${capitalizedType}Posts`] += this.socialActionsData[post.id].likes;
                    this[`totalComments${capitalizedType}Posts`] += this.socialActionsData[post.id].comments;

                    // Calculate Engagement Rate for each type
                    this[`engagementRate${capitalizedType}Posts`] = 
                            ((this[`totalComments${capitalizedType}Posts`] + this[`totalLikes${capitalizedType}Posts`]) / postedPosts.length).toFixed(2);
                }
            });

            // Calculate best engagement rate
            const totalPostedPosts = postedPosts.length;
            if (totalPostedPosts > 0) {
                const engagementRates = types.map(type => {
                    const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
                    return (this[`totalLikes${capitalizedType}Posts`] + this[`totalComments${capitalizedType}Posts`]) / totalPostedPosts;
                });
                this.bestEngagementRate = Math.max(...engagementRates);
                const bestTypeIndex = engagementRates.indexOf(this.bestEngagementRate);
                const typeNames = ['Texte', 'Image', 'Vidéo', 'Article', 'Multi-image'];
                this.bestEngagementRatePostType = this.bestEngagementRate > 0 ? typeNames[bestTypeIndex] : 'Inconnu';
            } else {
                this.bestEngagementRate = 0;
                this.bestEngagementRatePostType = 'Inconnu';
            }

            
            this.isBestEngagementChartLoading = false;
            this.renderEngagementCharts();

        },

        calculateCampaignsKPIs() {
            this.isLoading = true;

            // Calculate campaign social actions
            this.campaignLikes = {};
            this.campaignComments = {};
            const completedCampaigns = this.filterCampaignsByAccount(this.selectedAccountForCampaigns);
            completedCampaigns.forEach(campaign => {
                let totalLikes = 0;
                let totalComments = 0;
                const campaignPosts = this.allLinkedinPosts.filter(post => post.campaign_id === campaign.id && post.status === 'posted');
                campaignPosts.forEach(post => {
                    if (this.socialActionsData[post.id]) {
                        totalLikes += this.socialActionsData[post.id].likes;
                        totalComments += this.socialActionsData[post.id].comments;
                    }
                });
                this.campaignLikes[campaign.id] = totalLikes;
                this.campaignComments[campaign.id] = totalComments;
            });

            this.isLoading = false;
            this.renderCampaignSocialActionsChart();
            this.renderCampaignEngagementRateChart();
        },

        // **Refactored: Calculate Top Performant Posts**
        calculateTopPerformantPosts() {
            const publishedPosts = this.allLinkedinPosts.filter(post => post.status === 'posted');
            this.topPerformantPosts = publishedPosts.map(post => {
                const socialActions = this.socialActionsData[post.id] || { likes: 0, comments: 0 };
                const engagementRate = (socialActions.likes + socialActions.comments) / publishedPosts.length;
                return { post, engagementRate };
            }).sort((a, b) => b.engagementRate - a.engagementRate).slice(0, 3);
        },

        // **Refactored: Calculate Engagement by Period**
        calculateEngagementByPeriod() {
            this.isLoading = true;
            this.engagementPeriodError = '';
            
            const now = new Date();
            let startDate = this.selectedDateRange === 'all' ? null : new Date(now.setDate(now.getDate() - parseInt(this.selectedDateRange, 10)));
            if (startDate) startDate.setHours(0, 0, 0, 0);
            const endDate = new Date();
            endDate.setHours(0, 0, 0, 0);

            let posts = this.allLinkedinPosts.filter(post => post.status === 'posted');
            if(this.selectedAccountForEngagementPerPeriod) {
                posts = posts.filter(post => post.linkedin_user_id === this.selectedAccountForEngagementPerPeriod.id);
            }

            if(startDate) {
                posts = posts.filter(post => {
                    const postDate = new Date(post.scheduled_time);
                    postDate.setHours(0, 0, 0, 0);
                    return postDate >= startDate && postDate <= endDate;
                });
            }

            if (posts.length === 0) {
                this.noData = true;
                this.isLoading = false;
                this.renderEngagementPerPeriodChart();
                return;
            }

            const engagementByDate = {};
            posts.forEach(post => {
                const postDate = new Date(post.scheduled_time);
                postDate.setHours(0, 0, 0, 0);
                const dateKey = postDate.toISOString().split('T')[0];
                if (!engagementByDate[dateKey]) engagementByDate[dateKey] = { likes: 0, comments: 0 };
                const socialActions = this.socialActionsData[post.id] || { likes: 0, comments: 0 };
                engagementByDate[dateKey].likes += socialActions.likes;
                engagementByDate[dateKey].comments += socialActions.comments;
            });

            this.engagementByDate = engagementByDate;
            this.isLoading = false;
            this.renderEngagementPerPeriodChart();
        },

        setCurrentSection(section) {
            this.destroyCharts();
            this.currentSection = section;
            this.$nextTick(() => {
                this.renderCharts();
            });
        },

        destroyCharts() {
            const chartInstances = [
                this.textEngagementChartInstance,
                this.imageEngagementChartInstance,
                this.videoEngagementChartInstance,
                this.articleEngagementChartInstance,
                this.engagementPerPeriodChartInstance,
                this.barChartInstance,
                this.doughnutSuccessRateChartInstance,
                this.campaignSocialActionsChartInstance,
                this.campaignEngagementRateChartInstance,
                this.postVolumeByCampaignChartInstance,
                this.campaignStatusDistributionChart
            ];
            chartInstances.forEach(instance => {
                if (instance) {
                    instance.destroy();
                    instance = null;
                }
            });
            this.textEngagementChartInstance = null;
            this.imageEngagementChartInstance = null;
            this.videoEngagementChartInstance = null;
            this.articleEngagementChartInstance = null;
            this.engagementPerPeriodChartInstance = null;
            this.barChartInstance = null;
            this.doughnutSuccessRateChartInstance = null;
            this.campaignSocialActionsChartInstance = null;
            this.campaignEngagementRateChartInstance = null;
            this.postVolumeByCampaignChartInstance = null;
            this.campaignStatusDistributionChart = null;
        },

        selectAccount(account) {
            this.chosenAccount = account;
            this.chooseAccountDropdown = false;
            this.calculateAllKPIs();
        },

        selectAccountForEngagementPerPeriod(account) {
            this.selectedAccountForEngagementPerPeriod = account;
            this.engagementPerPeriodAccountDropdown = false;
            this.noData = false;
        },

        selectAccountForCampaigns(account) {
            this.selectedAccountForCampaigns = account;
            this.campaignsSectionDropdown = false;
            this.calculateCampaignsKPIs();
        },

        filterPostsByStatus(account = null) {
            let posts = this.allLinkedinPosts.filter(post => post.status === 'posted');
            if (account) posts = posts.filter(post => post.linkedin_user_id === account.id);
            return posts;
        },

        filterCampaignsByAccount(account = null) {
            let campaigns = this.allCampaigns.filter(campaign => campaign.status === 'completed');
            if(account) {
                campaigns = campaigns.filter(campaign => campaign.linkedin_user_id === account.id);
            }
            return campaigns;
        },
        
        getUserLinkedinInfo(linkedinUserId) {
            return this.allLinkedinAccounts.find(account => account.id === linkedinUserId);
        },

        calculateAllTimeAveragePostsPerDay() {
            const postedPosts = this.allLinkedinPosts.filter(post => post.status === 'posted');
            if (postedPosts.length === 0) return 0;
            const postDates = postedPosts.map(post => new Date(post.scheduled_time));
            const earliestDate = new Date(Math.min(...postDates));
            const now = new Date();
            const totalDays = Math.ceil((now - earliestDate) / (1000 * 60 * 60 * 24));
            return Math.round((postedPosts.length / totalDays) * 100) / 100;
        },

        // **Chart Rendering Methods (Unchanged)**
        renderCampaignSocialActionsChart() {
            const canvas = document.getElementById('campaignSocialActionsChart');
            if (!canvas) return console.error('Campaign Social Actions chart canvas not found');
            if (this.campaignSocialActionsChartInstance) this.campaignSocialActionsChartInstance.destroy();
            
            const campaignIds = Object.keys(this.campaignLikes);
            const campaigns = this.allCampaigns.filter(campaign => campaignIds.includes(campaign.id.toString()));
            if (!campaigns.length) {
                canvas.parentElement.innerHTML = 'Pas de données pour le moment';
                return;
            }
            
            const ctx = canvas.getContext('2d');
            const labels = campaigns.map(campaign => campaign.name);
            const likesData = campaigns.map(campaign => this.campaignLikes[campaign.id] || 0);
            const commentsData = campaigns.map(campaign => this.campaignComments[campaign.id] || 0);
            
            this.campaignSocialActionsChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        { label: 'Likes', data: likesData, backgroundColor: 'rgba(54, 162, 235, 0.5)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 },
                        { label: 'Commentaires', data: commentsData, backgroundColor: 'rgba(255, 206, 86, 0.5)', borderColor: 'rgba(255, 206, 86, 1)', borderWidth: 1 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Nombre d\'Interactions', color: 'white' }, ticks: { color: 'white' }, grid: { color: 'rgb(255, 255, 255, 0.2)' } },
                        x: { title: { display: true, text: 'Campagnes', color: 'white' }, ticks: { color: 'white' } }
                    },
                    plugins: { legend: { position: 'top', labels: { color: 'white' } }, title: { display: true, text: 'Interactions par Campagne', color: 'white', font: { size: 16 } } }
                }
            });
        },

        renderCampaignEngagementRateChart() {
            const canvas = document.getElementById("campaignEngagementRateChart");
            if (!canvas) {
                console.warn("Campaign Engagement Rate Chart canvas not found");
                return;
            }
            if (this.campaignEngagementRateChartInstance) this.campaignEngagementRateChartInstance.destroy();
            
            const campaignIds = Object.keys(this.campaignLikes);
            const campaigns = this.allCampaigns.filter(campaign => campaignIds.includes(campaign.id.toString()));
            if (!campaigns.length) {
                canvas.parentElement.innerHTML = 'Pas de données pour le moment';
                return;
            }
            
            const ctx = canvas.getContext('2d');
            const labels = campaigns.map(campaign => campaign.name);
            const engagementRateData = campaigns.map(campaign => {
                const likes = this.campaignLikes[campaign.id] || 0;
                const comments = this.campaignComments[campaign.id] || 0;
                const postCount = this.allLinkedinPosts.filter(post => post.campaign_id === campaign.id && post.status === 'posted').length;
                return postCount > 0 ? (likes + comments) / postCount : 0;
            });
            
            this.campaignEngagementRateChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Taux d\'Engagement',
                            data: engagementRateData,
                            backgroundColor: 'rgba(153, 102, 255, 0.5)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Taux d\'Engagement', color: 'white' }, ticks: { color: 'white' }, grid: { color: 'rgb(255, 255, 255, 0.2)' } },
                        x: { title: { display: true, text: 'Campagnes', color: 'white' }, ticks: { color: 'white' } }
                    },
                    plugins: { legend: { position: 'top', labels: { color: 'white' } }, title: { display: true, text: 'Taux d\'Engagement par Campagne', color: 'white', font: { size: 16 } } }
                }
            });
        },

        renderEngagementPerPeriodChart() {
            const canvas = document.getElementById('engagementPerPeriodChart');
            if (!canvas) return console.error('Engagement Per Period chart canvas not found');
            if (this.engagementPerPeriodChartInstance) this.engagementPerPeriodChartInstance.destroy();
            const dates = Object.keys(this.engagementByDate).sort();
            if (!dates.length) {
                this.noData = true;
                return;
            }
            const likesData = dates.map(date => this.engagementByDate[date].likes);
            const commentsData = dates.map(date => this.engagementByDate[date].comments);
            const ctx = canvas.getContext('2d');
            this.engagementPerPeriodChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [
                        { label: 'Likes', data: likesData, backgroundColor: 'rgba(54, 162, 235, 1)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 },
                        { label: 'Commentaires', data: commentsData, backgroundColor: 'rgba(255, 206, 86, 1)', borderColor: 'rgba(255, 206, 86, 1)', borderWidth: 1 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Nombre d\'Interactions', color: 'white' }, ticks: { color: 'white' }, grid: { color: 'rgb(255, 255, 255, 0.2)' } },
                        x: { title: { display: true, text: 'Date', color: 'white' }, ticks: { color: 'white' } }
                    },
                    plugins: { legend: { position: 'top', labels: { color: 'white' } }, title: { display: true, text: 'Engagement par Période', color: 'white', font: { size: 16 } } }
                }
            });
        },

        renderCharts() {
            if (this.currentSection === 'posts') {
                this.renderPostsCharts();
            } else if (this.currentSection === 'campaigns') {
                this.renderCampaignsChart();
            }
        },

        renderPostsCharts() {
            this.renderEngagementCharts();
            this.renderBarChart();
            this.renderPublishSuccessRateChart();
            this.calculateEngagementByPeriod();
        },

        renderCampaignsChart() {
            this.renderPostVolumeByCampaignChart();
            this.renderCampaignStatusDistributionChart();
            this.renderCampaignSocialActionsChart();
            this.renderCampaignEngagementRateChart();
        },

        renderBarChart() {
            const canvas = document.getElementById('postTypeDistributionChart');
            if (!canvas) return console.error('Bar chart canvas not found');
            if (this.barChartInstance) this.barChartInstance.destroy();
            const ctx = canvas.getContext('2d');
            this.barChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Texte', 'Image', 'Vidéo', 'Article', 'Multi-image'],
                    datasets: [{
                        label: 'Nombre des Posts',
                        data: Object.values(this.postTypeDistributionData),
                        backgroundColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(255, 114, 240, 1)', 'rgba(0, 255, 38, 1)'],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(255, 114, 240, 1)', 'rgba(0, 255, 38, 1)'],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, title: { display: true, text: 'Nombre des Posts par Type', font: { size: 16 }, color: 'white' } },
                    scales: {
                        y: { ticks: { color: 'white' }, grid: { color: 'rgb(255, 255, 255, 0.2)' }, beginAtZero: true, title: { display: true, text: 'Nombre de Posts', font: { size: 16 }, color: 'white' } },
                        x: { title: { display: true, text: 'Type de Post', font: { size: 16 }, color: 'white' } }
                    },
                },
            });
        },

        renderPublishSuccessRateChart() {
            const canvas = document.getElementById('postPuslishSuccesRate');
            if (!canvas) return console.error('Doughnut chart canvas not found');
            if (this.doughnutSuccessRateChartInstance) this.doughnutSuccessRateChartInstance.destroy();
            const ctx = canvas.getContext('2d');
            this.doughnutSuccessRateChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Publié', 'Échoué', 'En attente', 'Brouillons'],
                    datasets: [{
                        label: 'Statut des Posts',
                        data: Object.values(this.postStatusDistributionData),
                        backgroundColor: ['rgba(0, 255, 38, 1)', 'rgba(255, 0, 21, 1)', 'rgba(0, 119, 255, 1)', 'rgba(255, 145, 0, 1)'],
                        borderColor: ['rgba(0, 255, 38, 1)', 'rgba(255, 0, 21, 1)', 'rgba(0, 119, 255, 1)', 'rgba(255, 145, 0, 1)'],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: 'white', font: { size: 12 } } }, title: { display: true, text: 'Distribution des statuts de Posts', color: 'white' } },
                },
            });
        },

        renderCampaignStatusDistributionChart() {
            const canvas = document.getElementById('campaignStatusDistributionChart');
            if (!canvas) return console.error('Doughnut chart canvas not found');
            if (this.campaignStatusDistributionChart) this.campaignStatusDistributionChart.destroy();
            
            const data = this.campaignStatusDistributionData;
            const total = Object.values(data).reduce((sum, count) => sum + count, 0);
            if (total === 0) {
                canvas.parentElement.innerHTML = 'Pas de données pour le moment';
                return;
            }
            
            const ctx = canvas.getContext('2d');
            this.campaignStatusDistributionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Complété', 'Échoué', 'En cours', 'Brouillons'],
                    datasets: [{
                        label: 'Statut des Campagnes',
                        data: Object.values(data),
                        backgroundColor: ['rgba(0, 255, 38, 1)', 'rgba(255, 0, 21, 1)', 'rgba(0, 119, 255, 1)', 'rgba(255, 145, 0, 1)'],
                        borderColor: ['rgba(0, 255, 38, 1)', 'rgba(255, 0, 21, 1)', 'rgba(0, 119, 255, 1)', 'rgba(255, 145, 0, 1)'],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: 'white', font: { size: 12 } } }, title: { display: true, text: 'Distribution des statuts de Campagnes', color: 'white' } },
                },
            });
        },

        renderEngagementCharts() {
            ['text', 'image', 'video', 'article'].forEach(this.renderTypeEngagementChart);
        },

        renderTypeEngagementChart(type) {
            const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
            const canvasId = `${type}EngagementChart`;
            const chartInstanceProperty = `${type}EngagementChartInstance`;
            const canvas = document.getElementById(canvasId);
            if (!canvas) return console.error(`${capitalizedType} engagement chart canvas not found`);
            if (this[chartInstanceProperty]) this[chartInstanceProperty].destroy();
            const totalLikes = this[`totalLikes${capitalizedType}Posts`] || 0;
            const totalComments = this[`totalComments${capitalizedType}Posts`] || 0;
            const totalPosts = this[`total${capitalizedType}Posts`] || 0;
            const ctx = canvas.getContext('2d');
            if (totalPosts === 0) {
                this.typeErrors[type] = `Aucun post ${type} trouvé ou données non disponibles`;
                this[chartInstanceProperty] = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: [`Aucune donnée pour ${type} posts`],
                        datasets: [{ data: [1], backgroundColor: ['rgba(50, 200, 200, 0.5)'], borderColor: ['rgba(200, 200, 200, 1)'] }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false }, title: { display: true, text: `${capitalizedType} Posts: Aucune donnée disponible`, position: 'bottom', color: 'white' } },
                    },
                });
                return;
            }
            this[chartInstanceProperty] = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Likes', 'Commentaires'],
                    datasets: [{
                        data: [totalLikes, totalComments],
                        backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)'],
                        borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { color: 'white' } },
                        title: { display: true, position: 'bottom', text: `${totalPosts} ${type} post(s) - ${totalLikes + totalComments} interactions`, color: 'white' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = totalLikes + totalComments;
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                },
            });
        },

        renderPostVolumeByCampaignChart() {
            const canvas = document.getElementById('postVolumeByCampaignChart');
            if (!canvas) return console.error('Post Volume by Campaign chart canvas not found');
            if (this.postVolumeByCampaignChartInstance) this.postVolumeByCampaignChartInstance.destroy();
            
            const ctx = canvas.getContext('2d');
            const data = this.postVolumeByCampaign;
            if (!data.labels.length) {
                canvas.parentElement.innerHTML = 'Pas de données pour le moment';
                return;
            }
            
            this.postVolumeByCampaignChartInstance = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, title: { display: true, text: 'Nombres des Posts par Campagnes', color: 'white', font: { size: 16 } } },
                    scales: {
                        x: { beginAtZero: true, title: { display: true, text: 'Nombre des Posts', color: 'white' }, ticks: { color: 'white' } },
                        y: { title: { display: false }, ticks: { color: 'white', autoSkip: false, maxRotation: 0, minRotation: 0 }, grid: { color: 'rgb(255, 255, 255, 0.2)' } }
                    }
                }
            });
        },
    },
};
</script>


<style scoped>
.active-section {
    background-color: rgba(128, 128, 128, 0.445);
}
</style>