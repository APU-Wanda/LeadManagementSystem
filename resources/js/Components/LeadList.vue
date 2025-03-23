<template>
    <div>
        <h2>Lead Management</h2>
        <input v-model="searchQuery" placeholder="Search leads..." @input="fetchLeads" />
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <tr v-for="lead in leads" :key="lead.id">
                <td>{{ lead.name }}</td>
                <td>{{ lead.email }}</td>
                <td>{{ lead.phone }}</td>
                <td>
                    <select v-model="lead.status" @change="updateLead(lead)">
                        <option>New</option>
                        <option>In Progress</option>
                        <option>Closed</option>
                    </select>
                </td>
                <td>
                    <button @click="deleteLead(lead.id)">Delete</button>
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return { leads: [], searchQuery: "" };
    },
    methods: {
        fetchLeads() {
            axios.get(`/api/leads?search=${this.searchQuery}`).then(res => {
                this.leads = res.data.data;
            });
        },
        updateLead(lead) {
            axios.put(`/api/leads/${lead.id}`, lead).then(() => {
                alert("Lead updated!");
            });
        },
        deleteLead(id) {
            axios.delete(`/api/leads/${id}`).then(() => {
                this.fetchLeads();
            });
        },
    },
    mounted() {
        this.fetchLeads();
    },
};
</script>
