<template>
    <div class="sidebar">
        <div class="title">
            
        </div>
        <div class="menu-items" >
            <router-link to="/vm" active-class="active" tag="button" exact class="side-btn">
                <div class="link-container">
                    Vm
                </div>
            </router-link>
            <router-link to="/client" active-class="active" tag="button" exact class="side-btn">
                <div class="link-container">
                    Client
                </div>
            </router-link>
            <a href="" class="logout-button" v-on:click="logout">Logout</a>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
    logout: function(event) {
      event.preventDefault();
      this.$store.dispatch("Logout");
    }
  }
}
</script>

<style scoped>
.title {
    color: white;
    font-size: 24px;
    margin-top: 10px;
}
.menu-items {
    display: flex;
    flex-direction: column;
    margin-top: 40px;
    margin-left: 6px;
}
.menu-items > * {
    margin-top: 60px;
}
.side-btn {
    border: none;
    padding: 16px 0px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    color: white;
    background-color: transparent;
    font-weight: bold;
    color: #66819c;
}
.side-btn:focus {
    outline: none;
}
.side-btn.active {
    position: relative;
    color: #42b983;
    font-weight: 600;
    margin-left: 10px;
    border-radius: 30px 0 0 30px;
    background: rgba(255, 255, 255, .06);
    border-left: 1px solid rgba(255, 255, 255, .3);
    backdrop-filter: blur(10px);
    box-shadow: 20px 20px 40px -6px rgba(0, 0, 0, .2);
    text-align: center;
    position: relative;
}
.side-btn.active::before {
    top: -30px;
}
.side-btn.active::after {
    bottom: -30px;
}
.side-btn.active::before, .side-btn.active::after {
    position: absolute;
    content: "";
    right: 0;
    height: 30px;
    width: 30px;
    background: rgba(255, 255, 255, .06);
    border-left: 1px solid rgba(255, 255, 255, .3);
}
.side-btn.active .link-container::before {
    top: -60px;
}
.side-btn.active .link-container::after {
    bottom: -60px;
    z-index: 99;
}
.side-btn.active .link-container::before, .side-btn.active .link-container::after {
    position: absolute;
    content: "";
    right: 0px;
    height: 60px;
    width: 60px;
    border-radius: 50%;
    background: linear-gradient(to right,#0D0D0D, #101010);
}
.logout-button {
    position: relative;
    text-decoration: none;
    font-weight: bold;
    color: #66819c;
    top: 30vh;
}
</style>