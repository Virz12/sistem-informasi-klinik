<template>

    <Head title="Wilayah" />

    <DashboardLayout>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header with Add Button -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Daftar Wilayah</h2>
                    <p class="text-sm text-gray-600">Kelola semua wilayah klinik</p>
                </div>
                <button @click="showCreate = true"
                    class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Wilayah</span>
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama wilayah
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(district, index) in districts.data" :key="district?.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ index + 1 }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ district?.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="openEdit(district)"
                                        class="text-teal-600 hover:text-teal-900 p-1 rounded transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button @click="openDelete(district)"
                                        class="text-red-600 hover:text-red-900 p-1 rounded transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="m-3 flex justify-center gap-2">
                <button v-for="link in districts.links" :key="link.label" v-html="link.label" :disabled="!link.url"
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
                <h2 class="text-lg font-semibold mb-4">Tambah Wilayah</h2>
                <form @submit.prevent="store">
                    <input v-model="createForm.name" type="text" placeholder="Nama Wilayah"
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
                <h2 class="text-lg font-semibold mb-4">Edit Wilayah</h2>
                <form @submit.prevent="update">
                    <input v-model="editForm.name" type="text" placeholder="Nama Wilayah"
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
                    Hapus Wilayah
                </h2>
                <p>Anda yakin ingin menghapus wilayah ini?</p>
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
import { computed, ref } from 'vue';

// Modal state
const showCreate = ref(false)
const showEdit = ref(false)
const showDelete = ref(false)

const districts = computed(() => usePage().props.districts) ?? []

const goToPage = (url) => {
    if (!url) return
    router.visit(url, { preserveScroll: true, preserveState: true })
}

// Forms
const createForm = useForm({
    name: '',
})

const editForm = useForm({
    id: null,
    name: '',
})

const deleteForm = useForm({
    id: null,
})

// Actions
const openEdit = (district) => {
    editForm.id = district.id
    editForm.name = district.name
    showEdit.value = true
}

const openDelete = (district) => {
    deleteForm.id = district.id ?? ''
    showDelete.value = true
}

const store = () => {
    createForm.post(route('admin.district.store'), {
        onSuccess: () => {
            showCreate.value = false
            createForm.reset()
            router.reload()
        },
    })
}

const update = () => {
    editForm.put(route('admin.district.update', editForm.id), {
        onSuccess: () => {
            showEdit.value = false
            editForm.reset()
            router.reload({ only: ['districts'] })
        },
    })
}

const destroy = () => {
    router.delete(route('admin.district.destroy', deleteForm.id), {
        onSuccess: () => {
            showDelete.value = false
            deleteForm.reset()
            router.reload({ only: ['districts'] })
        },
    })
}
</script>