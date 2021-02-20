<template>
  <div class="dashboard">
    <Sidebar/>
    <div class="content">
      <router-view/>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar'

export default {
  name: 'Dashboard',
  components: {
    Sidebar
  }
}
</script>

<style scoped>
  .dashboard {
    display: grid;
    grid-template-columns: 1fr 5fr;
    height: calc(100vh - 45px);
}
.content {
    /* background-color: white;
    border-radius: 10px;*/
    margin: 0px 25px 25px 0px; 
    background: rgba(255, 255, 255, .06);
    border-radius: 20px;
    border-top: 1px solid rgba(255, 255, 255, .3);
    backdrop-filter: blur(10px);
    box-shadow: 20px 20px 40px -6px rgba(0, 0, 0, .2);
    text-align: center;
    position: relative;
}
</style>