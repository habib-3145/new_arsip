<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { User } from "@/types";
import DetailIjazah from "./DetailIjazah.vue";

const column = createColumnHelper<User>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const showDetail = ref(false);
const selectedData = ref<any>(null);

function openDetail(row: any) {
    selectedData.value = row;
    showDetail.value = true;
}

const { delete: deleteUser } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

const columns = [
    column.accessor("no", { header: "#" }),
    column.accessor("nama", { header: "Nama" }),
    column.accessor("kelas", { header: "Kelas" }),

    column.display({
        header: "Jurusan",
        cell: (cell: any) => cell.row.original.siswa?.jurusan ?? "-",
    }),

    column.accessor("nisn", { header: "NISN" }),
    column.accessor("tanggal", { header: "Tanggal" }),

    column.accessor("foto", {
        header: "Foto",
        cell: (cell) => {
            const path = cell.getValue();
            return h("img", {
                src: path ? `/storage/${path}` : "/no-image.png",
                style: "width:50px;height:50px;object-fit:cover;border-radius:6px;",
            });
        },
    }),

    column.accessor("id", {
        header: "Aksi",
        cell: (cell) =>
            h("div", { class: "d-flex gap-2" }, [

                // 🔍 DETAIL
                h("button", {
                    class: "btn btn-sm btn-icon btn-primary",
                    onClick: () => openDetail(cell.row.original),
                }, h("i", { class: "la la-eye fs-2" })),

                // ✏ EDIT
                h("button", {
                    class: "btn btn-sm btn-icon btn-info",
                    onClick: () => {
                        selected.value = cell.getValue();
                        openForm.value = true;
                    },
                }, h("i", { class: "la la-pencil fs-2" })),

                // 🗑 HAPUS
                h("button", {
                    class: "btn btn-sm btn-icon btn-danger",
                    onClick: () => deleteUser(`/master/dokumen/ijazah/${cell.getValue()}`),
                }, h("i", { class: "la la-trash fs-2" })),
            ]),
    })
];

const refresh = () => paginateRef.value.refetch();

watch(openForm, (val) => {
    if (!val) selected.value = "";
    window.scrollTo(0, 0);
});
</script>


<template>
    <Form :selected="selected" @close="openForm = false" v-if="openForm" @refresh="refresh" />

    <div class="card">
        <div class="card-header align-items-center">
            <h2 class="mb-0">List Ijazah Siswa</h2>
            <button type="button" class="btn btn-sm btn-primary ms-auto" v-if="!openForm" @click="openForm = true">
                Tambah
                <i class="la la-plus"></i>
            </button>
        </div>
        
        <div class="card-body">
            <paginate
                ref="paginateRef"
                id="table-ijazah"
                url="/master/dokumen/ijazah"
                :columns="columns"
            />
        </div>
    </div>

    <!-- ================= MODAL DETAIL ADA DI SINI ================= -->
    <DetailIjazah
        :show="showDetail"
        :data="selectedData"
        @close="showDetail = false"
    />
</template>

