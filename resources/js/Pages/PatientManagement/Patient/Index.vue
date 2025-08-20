<template>

    <Head title="Patient List" />

    <DashboardLayout>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header with Add Button -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Daftar Pasien</h2>
                    <p class="text-sm text-gray-600">Kelola semua pasien</p>
                </div>
                <button @click="showCreate = true"
                    class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Daftarkan Pasien</span>
                </button>
            </div>

            <!-- Patient List -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <!-- Patient Card -->
                    <div v-for="patient in patients.data" :key="patient?.id"
                        class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">{{ patient?.name }}</h3>
                                    <p class="text-xs text-gray-600">Patient ID: {{ patient?.patient_id }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-1">
                                <button @click="openEdit(patient)"
                                    class="p-1.5 text-gray-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                                <button @click="openDelete(patient)"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1 mb-3">
                            <p class="text-xs text-gray-500">{{ patient?.phone }}</p>
                            <p class="text-xs text-gray-500">{{ patient?.address }} - {{ patient.district.name }}</p>
                        </div>

                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500">Daftar pada :</span>
                                <span class="text-gray-700">{{ patient?.created_at }}</span>
                            </div>
                        </div>
                        <div class="pt-3 border-gray-100">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500">Daftar Alergi :</span>
                                <span class="text-gray-700">{{ patient?.allergies }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pagination -->
            <div class="m-3 flex justify-center gap-2">
                <button v-for="link in patients.meta.links" :key="link.label" v-html="link.label" :disabled="!link.url"
                    :class="[
                        'px-3 py-1 border rounded',
                        link.active ? 'bg-teal-600 text-white' : 'bg-white',
                        !link.url ? 'opacity-50' : ''
                    ]" @click="goToPage(link.url)" />
            </div>
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreate" @close="showCreate = false">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Daftarkan Pasien</h2>
                <form @submit.prevent="store">
                    <input v-model="createForm.name" type="text" placeholder="Nama Pasien"
                        class="w-full border p-2 rounded mb-4" />
                    <div class="space-x-2 flex">
                        <input v-model="createForm.birth_date" type="date" class="flex-1 border p-2 rounded mb-4" />
                        <select v-model="createForm.district_id" class="flex-1 border p-2 rounded mb-4">
                            <option :value="null" selected disabled>Pilih Wilayah</option>
                            <option v-for="district in districts" :key="district.id" :value="district.id">
                                {{ district.name }}
                            </option>
                        </select>
                    </div>
                    <div class="space-x-2 flex">
                        <select v-model="createForm.gender" class="w-full border p-2 rounded mb-4">
                            <option value="" selected disabled>Pilih Jenis Kelamin</option>
                            <option value="pria">Pria</option>
                            <option value="wanita">Wanita</option>
                        </select>
                        <input v-model="createForm.phone" type="text" placeholder="Nomor Telepon"
                            class="w-full border p-2 rounded mb-4" />
                    </div>
                    <textarea v-model="createForm.address" placeholder="Alamat"
                        class="w-full border p-2 rounded mb-4" />
                    <textarea v-model="createForm.allergies" placeholder="Alergi"
                        class="w-full border p-2 rounded mb-4" />
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-3 py-1 bg-gray-400 text-white rounded"
                            @click="showCreate = false">
                            Batal
                        </button>
                        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Edit Modal -->
        <Modal :show="showEdit" @close="showEdit = false">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Edit Data Pasien</h2>
                <form @submit.prevent="update">
                    <input v-model="editForm.name" type="text" placeholder="Nama Pasien"
                        class="w-full border p-2 rounded mb-4" />
                    <div class="space-x-2 flex">
                        <input v-model="editForm.birth_date" type="date" class="flex-1 border p-2 rounded mb-4" />
                        <select v-model="editForm.district_id" class="flex-1 border p-2 rounded mb-4">
                            <option v-for="district in districts" :key="district.id" :value="district.id">
                                {{ district.name }}
                            </option>
                        </select>
                    </div>
                    <div class="space-x-2 flex">
                        <select v-model="editForm.gender" class="w-full border p-2 rounded mb-4">
                            <option value="pria">Pria</option>
                            <option value="wanita">Wanita</option>
                        </select>
                        <input v-model="editForm.phone" type="text" placeholder="Nomor Telepon"
                            class="w-full border p-2 rounded mb-4" />
                    </div>
                    <textarea v-model="editForm.address" placeholder="Alamat" class="w-full border p-2 rounded mb-4" />
                    <textarea v-model="editForm.allergies" placeholder="Alergi"
                        class="w-full border p-2 rounded mb-4" />
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-3 py-1 bg-gray-400 text-white rounded"
                            @click="showEdit = false">
                            Batal
                        </button>
                        <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDelete" @close="showDelete = false">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4 text-red-600">
                    Hapus Data Pasien
                </h2>
                <p>Anda yakin ingin menghapus Data ini?</p>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" class="px-3 py-1 bg-gray-400 text-white rounded" @click="showDelete = false">
                        Batal
                    </button>
                    <button @click="destroy" type="button" class="px-3 py-1 bg-red-600 text-white rounded">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </Modal>
    </DashboardLayout>
</template>

<script setup>
import Modal from '@/Components/Modal.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from "vue";

// Modal state
const showCreate = ref(false)
const showEdit = ref(false)
const showDelete = ref(false)

const patients = computed(() => usePage().props.patients) ?? []
const districts = computed(() => usePage().props.districts) ?? []

const goToPage = (url) => {
    if (!url) return
    router.visit(url, { preserveScroll: true, preserveState: true })
}

// Forms
const createForm = useForm({
    name: "",
    birth_date: "",
    gender: "",
    phone: "",
    district_id: null,
    address: "",
    allergies: "",
})

const editForm = useForm({
    id: null,
    name: "",
    birth_date: "",
    gender: "",
    phone: "",
    district_id: null,
    address: "",
    allergies: "",
})

const deleteForm = useForm({
    id: null,
})

// Actions
const openEdit = (patient) => {
    editForm.id = patient.id;
    editForm.name = patient.name;
    editForm.birth_date = patient.birth_date;
    editForm.gender = patient.gender;
    editForm.phone = patient.phone;
    editForm.district_id = patient.district_id;
    editForm.address = patient.address;
    editForm.allergies = patient.allergies;

    showEdit.value = true
}

const openDelete = (patient) => {
    deleteForm.id = patient.id ?? ''
    showDelete.value = true
}

// Submission
const store = () => {
    createForm.post(route('patient_management.patient.store'), {
        onSuccess: () => {
            showCreate.value = false
            createForm.reset()
            router.reload({ only: ['patients'] })
        },
    })
}

const update = () => {
    editForm.put(route('patient_management.patient.update', editForm.id), {
        onSuccess: () => {
            showEdit.value = false
            editForm.reset()
            router.reload({ only: ['patients'] })
        },
    })
}

const destroy = () => {
    router.delete(route('patient_management.patient.destroy', deleteForm.id), {
        onSuccess: () => {
            showDelete.value = false
            deleteForm.reset()
            router.reload({ only: ['patients'] })
        },
    })
}
</script>
