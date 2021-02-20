<template>
  <div class="vm">
    <h1>Mes machines virtuelles</h1>
    <h2 v-if="currentUser">Votre compte : </h2>
    <p v-if="currentUser">{{ currentUser }}</p>
    <h2 v-if="userVMs">Vos machines virtuelles : </h2>
    <p v-if="userVMs">{{ userVMs }}</p>
    <div class="admin" v-if="allUsers">
      <h2>Tous les comptes : </h2>
      <p v-if="allUsers">{{ allUsers }}</p>
    </div>
    <button v-on:click="createVm">Create Default</button>
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
    this.$store.dispatch("getVms").then( res => {
      this.userVMs = res.data.data;
    })
    .catch( error => {
      console.log('error');
    });
  },
  methods: {
    createVm: function (evnet) {
      console.log('test');
      this.$store.dispatch("createVm").then( res => {
        console.log(res);
        this.$store.dispatch("getVms").then( res => {
          this.userVMs = res.data.data;
        })
        .catch( error => {
          console.log('error');
        });
      }).catch( error => {
      console.log(error);
    });
    }
  }
}
</script>


<style scoped>

</style>