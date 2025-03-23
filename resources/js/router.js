import { createRouter, createWebHistory } from "vue-router";
import LeadList from "./components/LeadList.vue";
import LeadImport from "./components/LeadImport.vue";
import Leads from '../Pages/Leads.vue';

const routes = [
    { path: "/", component: LeadList },
    { path: "/import", component: LeadImport },
    { path: '/leads', component: Leads },

];

export default createRouter({
    history: createWebHistory(),
    routes,
});
