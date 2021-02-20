<template>
  <div id="app">
    <div id="nav">
      <router-link to="/">Home</router-link>
      <router-link to="/about">About</router-link>
      <router-link to="/overview">Dashboard</router-link>
      <!-- <router-link v-if="!isLoggedIn" to="/login"> Login |</router-link>
      <a href="" v-if="isLoggedIn" v-on:click="logout"> Logout |</a> -->
    </div>
    <router-view/>
  </div>
</template>

<script>
export default {
  created() {
    this.$store.dispatch("ValideToken");
  },
  computed: {
    isLoggedIn: function() {
      return this.$store.state.login.isLoggedIn;
    }
  },
  methods: {
    logout: function(event) {
      event.preventDefault();
      this.$store.dispatch("Logout");
    }
  }
}
</script>

<style>
#app {
  font-family: 'Roboto', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #66819c;
}

body {
  background: linear-gradient(to right, #000000, #5e5e5e);
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

#nav {
  height: 25px;
  padding: 10px;
  line-height: 25px;
}

#nav a {
  font-weight: bold;
  color: #66819c;
  text-decoration: none;
  margin: 0 5%;
}

#nav a.router-link-exact-active {
  color: #42b983;
} 
</style>
