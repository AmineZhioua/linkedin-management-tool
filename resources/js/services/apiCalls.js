import axios from "axios";
import Swal from "sweetalert2";
import { useToast } from "vue-toastification";

class ApiService {
    constructor() {
        this.toast = useToast();
    }

    // FUNCTION TO MAKE A INTERACTION BOOST REQUEST
    async requestBoost(post, nbLikes, nbComments, message) {
        try {
            if(nbLikes <= 0 && nbComments <= 0) {
                this.toast.error("Veuillez demander au moins une Like ou un Commentaire");
                return;
            }

            const boostData = new FormData();

            const postUrl = `https://www.linkedin.com/feed/activity/${post.post_urn}`;

            // ADD THE VALIDATION BASED ON THE USER'S SUBSCRIPTION IN THE FUTURE
            boostData.append("post_id", post.id);
            boostData.append("linkedin_user_id", post.linkedin_user_id);
            boostData.append("post_url", postUrl);
            boostData.append("nb_likes", nbLikes);
            boostData.append("nb_comments", nbComments);
            boostData.append("message", message);

            const boostResponse = await axios.post("/boost-interaction/request", boostData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            });

            if (boostResponse.status === 201) {
                this.toast.success("La requête de Boost a été envoyé avec succès!");
                setTimeout(() => {
                    window.location.reload();
                }, 2000)
            } else if (boostResponse.status === 404) {
                this.toast.error("Post non trouvé !");
            }
        } catch (error) {
            console.error(error);
            this.toast.error("Une erreur s'est produite lors de l'envoi de la requête !");
        }
    }

    // FUNCTION TO DELETE A BOOST REQUEST FROM THE DATABASE
    async deleteBoostRequest(requestId) {
        const result = await Swal.fire({
            title: "Vous êtes sûr ?",
            text: "Vous ne pourrez pas revenir en arrière !",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Oui, supprimer !",
            cancelButtonText: "Annuler"
        });

        if (result.isConfirmed) {
            try {
                const response = await axios.delete("/boost-interaction/delete", {
                    data: {
                        request_id: requestId,
                    },
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                });
    
                if (response.status === 200) {
                    await Swal.fire({
                        title: "Supprimé !",
                        text: "Votre post a été supprimé.",
                        icon: "success"
                    });
                    window.location.reload();
                } else {
                    throw new Error("Failed to delete post");
                }
            } catch (error) {
                console.error("Error deleting post:", error);
                await Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Une erreur s'est produite lors de la suppression du post !",
                });
            }
        }
    }

    // FUNCTION TO UPDATE A BOOST REQUEST IN THE DATABASE
    async updateBoostRequest(requestId, nbLikes, nbComments, message) {
        try {
            const updateData = new FormData();

            updateData.append("request_id", requestId);
            updateData.append("nb_likes", nbLikes);
            updateData.append("nb_comments", nbComments);
            updateData.append("message", message);
            updateData.append('_method', 'PUT');

            const updateResponse = await axios.post("/boost-interaction/update", updateData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            });

            if(updateResponse === 200) {
                this.toast.success("Demande de Boost mise à jour avec succès !");
            } else if(updateResponse === 404) {
                this.toast.error("Demande de Boost à modifier n'existe pas !");
            }
        } catch(error) {
            console.error(error);
            this.toast.error("Une erreur s'est produite lors de la modification !");
        }
    }
}

export default new ApiService();