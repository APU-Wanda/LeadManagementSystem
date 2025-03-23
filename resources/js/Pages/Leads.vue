<script setup>
    import { ref, computed } from 'vue';
    import { usePage, useForm, router } from '@inertiajs/vue3';
    const exportLeads = () => {
    window.location.href = route('leads.export');
};
    const props = defineProps({ leads: Object });

    const search = ref('');
    const statusFilter = ref('');
    const fileInput = ref(null);
    const message = ref('');
    const error = ref('');

    const filteredLeads = computed(() => {
    return props.leads.data.filter(lead =>
    (!search.value || lead.name.toLowerCase().includes(search.value.toLowerCase())) &&
    (!statusFilter.value || lead.status === statusFilter.value)
    );
});

    // Function to trigger file selection
    const triggerFileInput = () => {
    fileInput.value.click();
};

    // Import Leads function
    const importLeads = (event) => {
    const file = event.target.files[0];

    if (!file) {
    error.value = "No file selected.";
    return;
}

    const form = useForm({ file });

    form.post(route('leads.import'), {
    forceFormData: true,
    onSuccess: () => {
    message.value = "Leads imported successfully!";
    error.value = "";
    router.reload(); // Refresh data instantly
},
    onError: (err) => {
    console.error("Import failed:", err);
    error.value = "Failed to import leads.";
}
});
};

    // Delete lead
    const deleteLead = (id) => {
    if (confirm('Are you sure you want to delete this lead?')) {
    router.delete(route('leads.destroy', id), {
    onSuccess: () => {
    props.leads.data = props.leads.data.filter(lead => lead.id !== id);
},
    onError: (error) => {
    console.error('Error deleting lead:', error);
}
});
}
};

    // Edit & Update Lead
    const editingLead = ref(null);
    const updatedLead = ref({ name: '', email: '', phone: '', status: '' });

    const editLead = (lead) => {
    editingLead.value = lead.id;
    updatedLead.value = { ...lead };
};

    const updateLead = () => {
    if (!editingLead.value) return;

    router.put(route('leads.update', { id: editingLead.value }), updatedLead.value, {
    onSuccess: () => {
    const index = props.leads.data.findIndex(lead => lead.id === editingLead.value);
    if (index !== -1) props.leads.data[index] = { ...updatedLead.value, id: editingLead.value };
    editingLead.value = null;
},
    onError: (error) => {
    console.error('Error updating lead:', error);
}
});
};

    const cancelEdit = () => {
    editingLead.value = null;
};

    
    const changePage = (url) => {
        router.get(url);
    };
</script>

<template>
    <div class="p-6 bg-gray-100 min-h-screen">
        <h1 class="text-2xl font-bold mb-4">Lead Management</h1>

        <!-- Search & Filter -->
        <div class="flex space-x-4 mb-4">
            <input type="text" v-model="search" placeholder="Search by name..." class="p-2 border rounded w-1/3" />
            <select v-model="statusFilter" class="p-2 border rounded w-1/3">
                <option value="">All Statuses</option>
                <option value="New">New</option>
                <option value="In Progress">In Progress</option>
                <option value="Closed">Closed</option>
            </select>
        </div>

        <!-- Leads Table -->
        <table class="w-full bg-white shadow rounded-lg">
            <thead>
            <tr class="bg-gray-200">
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Email</th>
                <th class="p-3 text-left">Phone</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 text-left">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="lead in filteredLeads" :key="lead.id" class="border-b">
                <td class="p-3">
                    <input v-if="editingLead === lead.id" v-model="updatedLead.name" type="text" class="border p-1 rounded" />
                    <span v-else>{{ lead.name }}</span>
                </td>
                <td class="p-3">
                    <input v-if="editingLead === lead.id" v-model="updatedLead.email" type="email" class="border p-1 rounded" />
                    <span v-else>{{ lead.email }}</span>
                </td>
                <td class="p-3">
                    <input v-if="editingLead === lead.id" v-model="updatedLead.phone" type="tel" class="border p-1 rounded" />
                    <span v-else>{{ lead.phone }}</span>
                </td>
                <td class="p-3">
                    <select v-if="editingLead === lead.id" v-model="updatedLead.status" class="border p-1 rounded">
                        <option value="New">New</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Closed">Closed</option>
                    </select>
                    <span v-else class="px-2 py-1 rounded" :class="{
                                'text-blue-500': lead.status === 'New',
                                'text-yellow-500': lead.status === 'In Progress',
                                'text-green-500': lead.status === 'Closed'
                            }">
                            {{ lead.status }}
                        </span>
                </td>
                <td class="p-3">
                    <template v-if="editingLead === lead.id">
                        <button @click="updateLead" class="text-green-600 px-2 py-1 rounded">Save</button>
                        <button @click="cancelEdit" class="text-gray-600 px-2 py-1 rounded">Cancel</button>
                    </template>
                    <template v-else>
                        <button @click="editLead(lead)" class="text-blue-600 px-2 py-1 rounded">Edit</button>
                        <button @click="deleteLead(lead.id)" class="text-red-800 px-2 py-1 rounded">Delete</button>
                    </template>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="mt-4 flex justify-center space-x-2">
            <template v-for="(link, index) in props.leads.links" :key="index">
                <button
                    v-if="link.url"
                    @click="changePage(link.url)"
                    class="px-3 py-1 border rounded-md"
                    :class="{'bg-blue-500 text-white': link.active, 'bg-gray-200': !link.active}"
                    v-html="link.label"
                ></button>
            </template>
        </div>

        <!-- Import & Export Buttons -->
        <div class="mt-4 flex space-x-4">
            <!-- Import Button -->
            <button @click="triggerFileInput" class="bg-green-500 text-green-900 px-4 py-2 rounded">Import Leads</button>
            <input ref="fileInput" type="file" @change="importLeads" accept=".xlsx,.xls,.csv" class="hidden">

            <!-- Export Button -->
            <button @click="exportLeads" class="bg-violet-600 text-blue-900 px-4 py-2 rounded">Export Leads</button>
        </div>

        <!-- Import Status Messages -->
        <p v-if="message" class="text-green-600">{{ message }}</p>
        <p v-if="error" class="text-red-600">{{ error }}</p>
    </div>
</template>
