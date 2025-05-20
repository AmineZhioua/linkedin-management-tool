<template>
    <div class="w-full h-full flex">
        <sidebar 
            @card-to-set="setCard"
        />

        <user-posts-card 
            v-if="cardToSet === 'userPosts'"
            :user-linkedin-accounts="userLinkedinAccounts" 
            :user-linkedin-posts="userLinkedinPosts" 
            :campaigns="campaigns" 
        />

        <kpi-section
            v-if="cardToSet === 'KPIs'"
            :all-linkedin-accounts="userLinkedinAccounts"
            :all-linkedin-posts="userLinkedinPosts"
            :all-campaigns="campaigns"
        /> 
    </div>
</template>


<script>
export default {
    name: 'MainSection',

    props: {
        userLinkedinAccounts: {
            type: Array,
            required: true,
        },

        userLinkedinPosts: {
            type: Array,
            required: true,
        },

        campaigns: {
            type: Array,
            required: true,
        },
    },

    data() {
        return {
            cardToSet: 'KPIs',
        };
    },

    methods: {
        setCard(card) {
            this.cardToSet = card;
        },
    },
};
</script>