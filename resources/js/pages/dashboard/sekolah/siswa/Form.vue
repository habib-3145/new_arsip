<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { Siswa } from "@/types";
import ApiService from "@/core/services/ApiService";
import { useRole } from "@/services/useRole";

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const siswa = ref<Siswa>({} as Siswa);
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const photo = ref<any>([]);
const formRef = ref();

/* ✅ VALIDASI DENGAN YUP */
const formSchema = Yup.object().shape({
    name: Yup.string().required("Nama harus diisi"),
    email: Yup.string()
        .email("Email harus valid")
        .required("Email harus diisi"),
    nis: Yup.string()
        .required("NIS harus diisi")
        .matches(/^\d+$/, "NIS hanya boleh berisi angka")
        .length(10, "NIS harus terdiri dari tepat 10 digit"),
    kelas: Yup.string().required("Kelas harus diisi"),
});

function getEdit() {
    block(document.getElementById("form-siswa"));
    ApiService.get("siswas", props.selected)
        .then(({ data }) => {
            siswa.value = data.siswa;
            photo.value = data.siswa.photo
                ? ["/storage/" + data.siswa.photo]
                : [];
        })
        .catch((err: any) => {
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-siswa"));
        });
}

function submit() {
    const formData = new FormData();
    formData.append("nama", siswa.value.nama);
    formData.append("email", siswa.value.email);
    formData.append("kelas", siswa.value.kelas);
    formData.append("nis", siswa.value.nis);

    block(document.getElementById("form-siswa"));
    axios({
        method: "post",
        url: props.selected
            ? `/siswas/${props.selected}`
            : "/siswa/data",
        data: formData,
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
        .then(() => {
            emit("close");
            emit("refresh");
            toast.success("Data berhasil disimpan");
            formRef.value.resetForm();
        })
        .catch((err: any) => {
            if (err.response?.data?.errors) {
                formRef.value.setErrors(err.response.data.errors);
            }
            toast.error(err.response?.data?.message || "Terjadi kesalahan");
        })
        .finally(() => {
            unblock(document.getElementById("form-siswa"));
        });
}

const role = useRole();
const roles = computed(() =>
    role.data.value?.map((item: Siswa) => ({
        id: item.id,
        text: item.full_name,
    }))
);

onMounted(async () => {
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
        id="form-siswa"
        ref="formRef"
    >
        <div class="card-header align-items-center">
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Siswa</h2>
            <button
                type="button"
                class="btn btn-sm btn-light-danger ms-auto"
                @click="emit('close')"
            >
                Batal
                <i class="la la-times-circle p-0"></i>
            </button>
        </div>

        <div class="card-body">
            <div class="row">
                <!-- NAMA -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Nama</label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="name"
                            autocomplete="off"
                            v-model="siswa.nama"
                            placeholder="Masukkan Nama"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="name" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- NIS -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">NIS</label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="nis"
                            autocomplete="off"
                            v-model="siswa.nis"
                            placeholder="Masukkan NIS"
                            @input="siswa.nis = siswa.nis.replace(/[^0-9]/g, '').slice(0, 10)"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="nis" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KELAS -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Kelas</label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="kelas"
                            autocomplete="off"
                            v-model="siswa.kelas"
                            placeholder="Masukkan Kelas"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="kelas" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EMAIL -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">Email</label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="email"
                            autocomplete="off"
                            v-model="siswa.email"
                            placeholder="Masukkan Email"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="email" />
                            </div>
                        </div>
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
