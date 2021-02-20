<template>
  <div class="login">
    <div class="container">
        <p v-if="errors.length">
          <b>Corriger les erreurs suivantes : </b>
          <ul>
            <li v-for="error in errors" :key="error.id">
              {{ error }}
            </li>
          </ul>
        </p>
        <form @submit="checkForm" action="">
            <p>Connexion</p>
            <input autocomplete="email" type="email" placeholder="Email" v-model="email">
            <p><input autocomplete="current-password" type="password" placeholder="Mot De Passe" v-model="password"></p>
            <p><input type="submit" value="Se connecter"></p>
        </form>
        <div class="drops">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data: function() {
    return {
      errors: [],
      email: null,
      password: null
    }
  },
  methods: {
    checkForm: function (e) {
      e.preventDefault();
      this.errors = [];
      if (this.email && this.password) {
        var args = { 
          'email': this.email,
          'password': this.password
        };
        this.$store.dispatch("Login",args).catch( error => {
          this.errors.push(error.error);
        });
        return true;
      }

      if (!this.email) {
        this.errors.push('Email requis');
      }
      if (!this.password) {
        this.errors.push('Mot de passe requis');
      }
    }
  }
}
</script>

<style>
.login {
    width: 100%;
    height: calc(100vh - 45px);
}
.container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    perspective: 400px;
    perspective-origin: 50% 50%;
}
.container:hover .drops div{
    animation: 1s disap forwards;
}
form {
    background: rgba(255, 255, 255, .06);
    padding: 3em;
    height: 320px;
    border-radius: 20px;
    border-left: 1px solid rgba(255, 255, 255, .3);
    border-top: 1px solid rgba(255, 255, 255, .3);
    backdrop-filter: blur(10px);
    box-shadow: 20px 20px 40px -6px rgba(0, 0, 0, .2);
    text-align: center;
    position: relative;
    transition: all .2s ease-in-out;
}
form p {
    font-weight: 500;
    color: white;
    opacity: 0.8;
    font-size: 1.4rem;
    margin-top: 0;
    margin-bottom: 60px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, .2);
}
form input {
    background: transparent;
    width: 200px;
    padding: 1em;
    margin-bottom: 2em;
    border: none;
    border-radius: 20px;
    border-left: 1px solid rgba(255, 255, 255, .3);
    border-top: 1px solid rgba(255, 255, 255, .3);
    backdrop-filter: blur(5px);
    box-shadow: 4px 4px 60px rgba(0, 0, 0, .2);
    color: white;
    font-weight: 500;
    transition: all .2s ease-in-out;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, .2);
}
form input:hover {
    background: rgba(255, 255, 255, .1);
    box-shadow: 4px 4px 60px 8px rgba(0, 0, 0, .2);
}
form input[type='email']:focus, form input[type='password']:focus {
    background: rgba(255, 255, 255, .1);
    box-shadow: 4px 4px 60px 8px rgba(0, 0, 0, .2);
}
form input[type='button'] {
    margin-top: 10px;
    width: 150px;
    font-size: 1rem;
}
form input[type='button']:hover {
    cursor: pointer;
}
::placeholder {
    font-weight: 400;
    color: white;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, .4);
}
input:focus, button:focus, select:focus {
    outline: none;
}
.drops div{
    background: rgba(255, 255, 255, .06);
    backdrop-filter: blur(10px);
    border-radius: 10px;
    border-left: 1px solid rgba(255, 255, 255, .3);
    border-top: 1px solid rgba(255, 255, 255, .3);
    box-shadow: 10px 10px 60px -8px rgba(0, 0, 0, .2);
    position: absolute;
    animation: .5s appear forwards;
    transition: all 1s ease;
    transform-style: preserve-3d;
    pointer-events: none;
}
.drops div:nth-child(1) {
    height: 80px;
    width: 80px;
    top: -20px;
    left: -40px;
    z-index: -1;
}
.drops div:nth-child(2) {
    height: 80px;
    width: 80px;
    bottom: -30px;
    right: -10px;
}
.drops div:nth-child(3) {
    height: 100px;
    width: 100px;
    bottom: 120px;
    right: -50px;
    z-index: -1;
}
.drops div:nth-child(4) {
    height: 120px;
    width: 120px;
    top: -60px;
    right: -60px;
}
.drops div:nth-child(5) {
    height: 100px;
    width: 100px;
    bottom: 20px;
    left: -70px;
}
@keyframes disap {
    from{
        opacity: 1;
    }
    to{
        opacity: 0;
        transform: translateZ(200px);
        display: none;
    }
}
@keyframes appear {
    from{
        display: block;
        transform: translateZ(200px);
        opacity: 0;
    }
    to{
        transform: translateZ(0px);
        opacity: 1;
    }
}

</style>
