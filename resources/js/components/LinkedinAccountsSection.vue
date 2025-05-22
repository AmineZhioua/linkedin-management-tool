<template>
  <div class="w-full h-full p-4 flex flex-col items-center justify-center">
    <!-- Table Section -->
    <div class="flex flex-col items-center w-full">
      <DataTable
        v-if="allLinkedinAccounts.length > 0"
        :value="allLinkedinAccounts"
        stripedRows
        class="w-full linkedin-table"
      >
        <Column header="Image" style="width: 100px;">
          <template #body="{ data }">
            <img
              :src="data.linkedin_picture || '/build/assets/images/default-profile.png'"
              alt="profile-picture"
              height="50"
              width="50"
              class="rounded-full"
            />
          </template>
        </Column>
        <Column header="Nom">
          <template #body="{ data }">
            <span class="text-lg fw-semibold">
              {{ data.linkedin_firstname }} {{ data.linkedin_lastname }}
            </span>
          </template>
        </Column>
        <Column header="Actions" style="width: 100px;">
          <template #body="{ data }">
            <Button
              icon="pi pi-trash"
              severity="danger"
              text
              rounded
              @click="showDeletePopup(data.linkedin_id)"
            />
          </template>
        </Column>
      </DataTable>
      <!-- No Accounts Message -->
      <div
        v-else
        class="flex flex-col items-center gap-2 w-full"
      >
        <Button
          @click="connectLink"
          label="Se Connecter avec LinkedIn"
          icon="pi pi-linkedin"
          class="social-btn p-button-rounded flex items-center gap-2 px-4 py-2"
        />
        <p class="text-black text-center">
          <u>Cette étape est obligatoire pour la création de ton dashboard</u>
        </p>
      </div>
      <!-- Add Account Button -->
      <div v-if="allLinkedinAccounts.length > 0" class="flex justify-center w-full mt-4">
        <Button
          @click="connectLink"
          label="Ajouter un compte"
          icon="pi pi-plus"
          class="p-button-rounded flex items-center gap-2 px-4 py-2"
        />
      </div>
    </div>

    <!-- Delete Confirmation Popup -->
    <div
      v-if="showDeletePopupFlag"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded-lg w-full max-w-sm">
        <button
          @click="showDeletePopupFlag = false"
          class="absolute top-2 right-2 p-2"
        >
          <img src="/build/assets/icons/close.svg" alt="close-icon" height="20" width="20" />
        </button>
        <img
          src="/build/assets/popups/sad-face.svg"
          alt="Sad Face"
          height="120"
          width="120"
          class="mx-auto"
        />
        <p class="mt-4 text-xl fw-semibold text-center">
          Êtes-vous sûr de vouloir supprimer ce compte LinkedIn ?
        </p>
        <div class="flex justify-center gap-4 mt-4">
          <button
            @click="confirmDelete"
            class="bg-red-600 text-white px-4 py-2 rounded-full"
          >
            Confirmer
          </button>
          <button
            @click="showDeletePopupFlag = false"
            class="bg-transparent border border-black text-black px-4 py-2 rounded-full"
          >
            Annuler
          </button>
        </div>
      </div>
    </div>

    <!-- Success Popup -->
    <div
      v-if="showSuccessPopup"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded-lg text-center w-full max-w-sm">
        <img
          src="/build/assets/popups/like-popup.svg"
          alt="Success"
          height="120"
          width="120"
          class="mx-auto"
        />
        <p class="mt-4 text-lg">{{ flashSuccess }}</p>
        <button
          @click="showSuccessPopup = false"
          class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-full"
        >
          Fermer
        </button>
      </div>
    </div>

    <!-- Error Popup -->
    <div
      v-if="showErrorPopup"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded-lg text-center w-full max-w-sm">
        <img
          src="/build/assets/popups/sad-face.svg"
          alt="Error"
          height="120"
          width="120"
          class="mx-auto"
        />
        <p class="mt-4 text-lg">{{ flashError }}</p>
        <button
          @click="showErrorPopup = false"
          class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-full"
        >
          Fermer
        </button>
      </div>
    </div>

    <!-- Payment Success Popup -->
    <div
      v-if="showPaymentSuccessPopup"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded-lg text-center w-full max-w-sm">
        <img
          src="/build/assets/popups/like-popup.svg"
          alt="Success"
          height="120"
          width="120"
          class="mx-auto"
        />
        <p class="mt-4 text-lg">{{ successPayment }}</p>
        <button
          @click="showPaymentSuccessPopup = false"
          class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-full"
        >
          Fermer
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

export default {
  name: 'LinkedinAccountsSection',
  components: {
    DataTable,
    Column,
    Button,
  },
  props: {
    user: {
      type: Object,
      required: true,
    },
    allLinkedinAccounts: {
      type: Array,
      required: true,
    },
    flashSuccess: {
      type: String,
      default: '',
    },
    flashError: {
      type: String,
      default: '',
    },
    successPayment: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      showDeletePopupFlag: false,
      deleteLinkedinId: null,
      showSuccessPopup: false,
      showErrorPopup: false,
      showPaymentSuccessPopup: false,
    };
  },
  mounted() {
    if (this.flashSuccess) {
      this.showSuccessPopup = true;
    }
    if (this.flashError) {
      this.showErrorPopup = true;
    }
    if (this.successPayment) {
      this.showPaymentSuccessPopup = true;
    }
  },
  methods: {
    connectLink() {
      const newTab = window.open('https://www.linkedin.com/m/logout', '_blank');
      setTimeout(() => {
        if (newTab) {
          newTab.close();
        }
        window.location.href = '/linkedin/auth';
      }, 2000);
    },
    showDeletePopup(linkedinId) {
      this.deleteLinkedinId = linkedinId;
      this.showDeletePopupFlag = true;
    },
    confirmDelete() {
      window.location.href = `/linkedin/delete?linkedin_id=${this.deleteLinkedinId}`;
    },
  },
};
</script>

<style scoped>
.fw-semibold {
  font-weight: 600;
}
.text-lg {
  font-size: 1.125rem;
  line-height: 1.75rem;
}
.rounded-full {
  border-radius: 9999px;
}

/* PrimeVue Table Customizations */
.linkedin-table {
  width: 100%;
  max-width: 800px;
  background-color: #ffffff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
:deep(.p-datatable .p-datatable-thead > tr > th) {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  padding: 10px;
  font-weight: 600;
  color: #333;
  text-align: left;
}
:deep(.p-datatable .p-datatable-tbody > tr) {
  transition: background-color 0.2s;
}
:deep(.p-datatable .p-datatable-tbody > tr:hover) {
  background-color: #f5f5f5;
}
:deep(.p-datatable .p-datatable-tbody > tr > td) {
  padding: 10px;
  border-bottom: 1px solid #dee2e6;
  text-align: left;
}

/* PrimeVue Button Customizations */
:deep(.p-button) {
  background-color: #ffffff;
  border: 1px solid #ccc;
  color: #333;
  transition: background-color 0.2s;
}
:deep(.p-button:hover) {
  background-color: #e0f7fa;
}
:deep(.p-button .p-button-icon) {
  color: #333;
}
.social-btn {
  background-color: #ffffff;
  border: 1px solid #ccc;
}
</style>
