<template>
  <div class="client">
    <h1>Overview de toutes les machines etc</h1>
    <h2 v-if="currentUser">Votre compte : </h2>
    <p v-if="currentUser">{{ currentUser }}</p>
    <h2 v-if="userVMs">Vos machines virtuelles : </h2>
    <p v-if="userVMs">{{ userVMs }}</p>
    <div class="admin" v-if="allUsers">
      <h2>Tous les comptes : </h2>
      <p v-if="allUsers">{{ allUsers }}</p>
    </div>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      currentUser: null,
      allUsers: null,
      userVMs: null
    }
  },
  created() {
    this.$store.dispatch("getUser").then( res => {
      this.currentUser = res.data.data;
    })
    .catch( error => {
      console.log('error');
    });
  }
}
</script>


<style scoped>

</style>