<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import ApiService from "@/core/services/ApiService";

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const formRef = ref();

const user = ref({
    name: "",
    no_kk: "",
    alamat: "",
    rt: "",
    rw: "",
    kode_pos: "",
    desa_kelurahan: "",
    kecamatan: "",
    kabupaten_kota: "",
    provinsi: "",
});

const formSchema = Yup.object().shape({
    name: Yup.string().required("Nama harus diisi"),
    no_kk: Yup.string()
        .required("Nomor KK harus diisi")
        .length(16, "Nomor KK harus 16 digit"),
    alamat: Yup.string().required("Alamat harus diisi"),
    rt: Yup.string().required("RT harus diisi"),
    rw: Yup.string().required("RW harus diisi"),
    kode_pos: Yup.string().required("Kode pos harus diisi"),
    desa_kelurahan: Yup.string().required("Desa/Kelurahan harus diisi"),
    kecamatan: Yup.string().required("Kecamatan harus diisi"),
    kabupaten_kota: Yup.string().required("Kabupaten/Kota harus diisi"),
    provinsi: Yup.string().required("Provinsi harus diisi"),
});

function getEdit() {
    block(document.getElementById("form-kartu-keluarga"));
    ApiService.get("master/dokumen/kartu-keluarga", props.selected)
        .then(({ data }) => {
            user.value = data.user;
        })
        .catch((err) => {
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}

function submit() {
    const formData = new FormData();

    Object.keys(user.value).forEach((key) => {
        formData.append(key, user.value[key]);
    });

    if (props.selected) {
        formData.append("_method", "PUT");
    }

    block(document.getElementById("form-user"));

    axios({
        method: "post",
        url: props.selected
            ? `/master/dokumen/kartu-keluarga/${props.selected}`
            : "/master/dokumen/kartu-keluarga/store",
        data: formData,
        headers: { "Content-Type": "multipart/form-data" },
    })
        .then(() => {
            emit("close");
            emit("refresh");
            toast.success("Data berhasil disimpan");
            formRef.value.resetForm();
        })
        .catch((err) => {
            formRef.value.setErrors(err.response.data.errors);
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}

onMounted(() => {
    if (props.selected) {
        getEdit();
    }
});

watch(
    () => props.selected,
    () => {
        if (props.selected) {
            getEdit();
        }
    }
);
</script>


<template>
    <VForm
        class="form card mb-10"
        @submit="submit"
        :validation-schema="formSchema"
        id="form-user"
        ref="formRef"
    >
        <div class="card-header align-items-center">
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Data KK</h2>
            <button
                type="button"
                class="btn btn-sm btn-light-danger ms-auto"
                @click="emit('close')"
            >
                Batal
            </button>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-6 mb-7" v-for="field in [
                    { key: 'name', label: 'Nama' },
                    { key: 'no_kk', label: 'Nomor KK' },
                    { key: 'alamat', label: 'Alamat' },
                    { key: 'rt', label: 'RT' },
                    { key: 'rw', label: 'RW' },
                    { key: 'kode_pos', label: 'Kode Pos' },
                    { key: 'desa_kelurahan', label: 'Desa/Kelurahan' },
                    { key: 'kecamatan', label: 'Kecamatan' },
                    { key: 'kabupaten_kota', label: 'Kabupaten/Kota' },
                    { key: 'provinsi', label: 'Provinsi' },
                ]">
                    <label class="form-label fw-bold fs-6 required">
                        {{ field.label }}
                    </label>

                    <Field
                        class="form-control form-control-lg form-control-solid"
                        type="text"
                        :name="field.key"
                        v-model="user[field.key]"
                        autocomplete="off"
                        :placeholder="'Masukkan ' + field.label"
                    />

                    <div class="fv-help-block">
                        <ErrorMessage :name="field.key" />
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-primary btn-sm ms-auto">
                Simpan
            </button>
        </div>
    </VForm>
</template>

