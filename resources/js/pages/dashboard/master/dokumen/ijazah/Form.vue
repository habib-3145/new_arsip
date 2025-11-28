<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, computed } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import ApiService from "@/core/services/ApiService";
import { useAllSiswa } from "@/services/useAllSiswa"; // <-- composable yang benar

const props = defineProps({
    selected: { type: String, default: null },
});

const emit = defineEmits(["close", "refresh"]);

const ijazah = ref<any>({});
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const foto = ref<any>([]);
const formRef = ref();


const { data, isLoading } = useAllSiswa(); // fetch otomatis


const siswas = computed(() =>
    data.value?.map((item: any) => ({
        id: item.id,
        text: item.jurusan,
    })) ?? []
);

/* ======================= AUTO FILL FORM ======================= */
// ketika select siswa dipilih → auto isi nama, nisn, kelas
// function onSelectSiswa(val:any) {
//     const selected = siswas.value.find(s => s.id == val);
//     if (selected) {
//         ijazah.value.nama  = selected.text;
//         ijazah.value.nisn  = selected.nisn;
//         ijazah.value.kelas = selected.kelas;
//     }
// }

/* ======================= VALIDASI ======================= */
const formSchema = Yup.object().shape({
    siswa_id: Yup.string().required("Siswa harus dipilih"),
    nama: Yup.string().required("Nama harus diisi"),
    nisn: Yup.string()
        .required("NISN harus diisi")
        .matches(/^\d+$/, "NISN hanya boleh berisi angka")
        .length(10, "NISN harus terdiri dari tepat 10 digit"),
    tanggal: Yup.string().required("Tanggal harus dipilih"),
    kelas: Yup.string().required("Kelas harus diisi"),
});

/* ======================= EDIT DATA ======================= */
function getEdit() {
    block(document.getElementById("form-user"));
    ApiService.get("master/ijazah", props.selected)
        .then(({ data }) => {
            ijazah.value = data.ijazah;
            foto.value = data.ijazah.foto ? ["/storage/" + data.ijazah.foto] : [];
        })
        .catch(err => toast.error(err.response.data.message))
        .finally(() => unblock(document.getElementById("form-user")));
}

/* ======================= SUBMIT ======================= */
function submit() {
    const formData = new FormData();
    formData.append("siswa_id", ijazah.value.siswa_id);
    formData.append("nama", ijazah.value.nama);
    formData.append("nisn", ijazah.value.nisn);
    formData.append("tanggal", ijazah.value.tanggal);
    formData.append("kelas", ijazah.value.kelas);

    if (foto.value.length) formData.append("foto", foto.value[0].file);
    if (props.selected) formData.append("_method", "PUT");

    block(document.getElementById("form-user"));
    axios({
        method: "post",
        url: props.selected ? `/master/dokumen/ijazah/${props.selected}` : "/master/dokumen/ijazah/store",
        data: formData,
        headers: { "Content-Type": "multipart/form-data" },
    })
        .then(() => {
            emit("close");
            emit("refresh");
            toast.success("Data berhasil disimpan");
            formRef.value.resetForm();
        })
        .catch(err => formRef.value.setErrors(err.response.data.errors))
        .finally(() => unblock(document.getElementById("form-ijazah")));
}

/* ======================= INIT ======================= */
onMounted(() => props.selected && getEdit());
</script>



<template>
    <VForm class="form card mb-10" @submit="submit" :validation-schema="formSchema" id="form-user" ref="formRef">

        <div class="card-header align-items-center">
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Ijazah</h2>
            <button type="button" class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
                Batal <i class="la la-times-circle p-0"></i>
            </button>
        </div>

        <div class="card-body">
            <div class="row">

                <!-- ======================= SISWA (SELECT2) ======================= -->
                
                <!-- ============================================================= -->

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Nama</label>
                        <Field class="form-control form-control-lg form-control-solid"
                            type="text" name="nama" v-model="ijazah.nama"
                            placeholder="Masukkan Nama" />
                        <ErrorMessage name="nama" class="text-danger" />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Kelas</label>
                        <Field class="form-control form-control-lg form-control-solid"
                            type="text" name="kelas" v-model="ijazah.kelas"
                            placeholder="Contoh: XII RPL" />
                        <ErrorMessage name="kelas" class="text-danger" />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Jurusan</label>

                        <Field name="siswa_id" v-slot="{ field, errors }">
                            <select2
                                class="form-select-solid"
                                placeholder="Pilih Jurusan"
                                :options="siswas"
                                v-model="ijazah.siswa_id"
                                v-bind="field"
                            />
                            <div class="fv-help-block text-danger">{{ errors[0] }}</div>
                        </Field>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">NISN</label>
                        <Field class="form-control form-control-lg form-control-solid"
                            type="text" name="nisn" v-model="ijazah.nisn" />
                        <ErrorMessage name="nisn" class="text-danger" />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Tanggal Ijazah</label>
                        <Field class="form-control form-control-lg form-control-solid"
                            type="date" name="tanggal" v-model="ijazah.tanggal" />
                        <ErrorMessage name="tanggal" class="text-danger" />
                    </div>
                </div>

                

                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">Foto Ijazah</label>
                        <file-upload :files="foto" :accepted-file-types="fileTypes"
                            @updatefiles="file => foto = file"></file-upload>
                        <ErrorMessage name="foto" class="text-danger" />
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-primary btn-sm ms-auto">Simpan</button>
        </div>
    </VForm>
</template>

