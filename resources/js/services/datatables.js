// FUNCTION TO FIND THE USER BASED ON THE ID
function getLinkedinUserByID(linkedinUsers, id) {
    return linkedinUsers.find(user => user.id === id);
};

// FUNCTION TO GET THE FULL LINKEDIN USERNAME BASED THE ID
function getUsername(linkedinUsers, id) {
    const account = getLinkedinUserByID(linkedinUsers, id);
    return account ? `${account.linkedin_firstname} ${account.linkedin_lastname}` : 'inconnu';
};

// FUNCTION TO GET THE PROFILE PICTURE BASED ON THE ID
function getProfilePicture(linkedinUsers, linkedinUserId) {
    const account = getLinkedinUserByID(linkedinUsers, linkedinUserId);
    if(account) {
        if(account.linkedin_picture != null) return account.linkedin_picture;
    }
    return '/build/assets/images/default-profile.png';
};

// GET THE CAMPAIGN COLOR BASED ON THE CAMPAIGN_ID
function getCampaignColor(campaignsArray, campaignId) {
    if(campaignId === null) {
        return '#FFFFFF00'
    }
    const campaign = campaignsArray.find(c => c.id === campaignId);
    return campaign ? campaign.color : 'inconnu';
};

// FORMAT THE GIVEN DATE TO A HUMAN READABLE TEXT
function formatDate(dateString) {
    const date = new Date(dateString);
    
    const daysOfWeek = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    const dayOfWeek = daysOfWeek[date.getDay()];
    
    const dayOfMonth = date.getDate();
    
    const year = date.getFullYear();
    
    let hours = date.getHours();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${dayOfWeek} ${dayOfMonth}, ${year} à ${hours}:${minutes}${ampm}`;
};

function formatDateForEngagement(dateString) {
    const date = new Date(dateString);
    
    const month = date.getMonth() + 1;
    
    const dayOfMonth = date.getDate();
    
    const year = date.getFullYear();
    
    
    return `${dayOfMonth}/${month}/${year}`;
};

function formatDateWithMonth(dateString) {
    const date = new Date(dateString);
    
    const daysOfWeek = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    const dayOfWeek = daysOfWeek[date.getDay()];
    
    const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aôut', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
    const month = months[date.getMonth()];

    const dayOfMonth = date.getDate();
    
    const year = date.getFullYear();
    
    let hours = date.getHours();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${dayOfWeek} ${dayOfMonth}, ${month} ${year} à ${hours}:${minutes}${ampm}`;
};


export {
    getLinkedinUserByID,
    getUsername,
    getProfilePicture,
    getCampaignColor,
    formatDate,
    formatDateForEngagement,
    formatDateWithMonth,
};