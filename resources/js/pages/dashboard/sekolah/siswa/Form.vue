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

const formSchema = Yup.object().shape({
    name: Yup.string().required("Nama harus diisi"),
    email: Yup.string()
        .email("Email harus valid")
        .required("Email harus diisi"),
    nis: Yup.string().required("Nis harus diisi"),
    kelas: Yup.string().required("Kelas harus diisi"),
    // phone: Yup.string().required("Nomor Telepon harus diisi"),
    // role_id: Yup.string().required("Pilih role"),
});

function getEdit() {
    block(document.getElementById("form-siswa"));
    ApiService.get("siswa", props.selected)
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
    formData.append("name", siswa.value.name);
    formData.append("email", siswa.value.email);
    formData.append("kelas", siswa.value.kelas);
    formData.append("nis", siswa.value.nis);

    if (siswa.value?.password) {
        formData.append("password", siswa.value.password);
        formData.append(
            "password_confirmation",
            siswa.value.passwordConfirmation
        );
    }
    if (photo.value.length) {
        formData.append("photo", photo.value[0].file);
    }
    if (props.selected) {
        formData.append("_method", "PUT");
    }

    block(document.getElementById("form-siswa"));
    axios({
        method: "post",
        url: props.selected
            ? `/siswa/data/${props.selected}`
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
            formRef.value.setErrors(err.response.data.errors);
            toast.error(err.response.data.message);
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
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Nama
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="name"
                            autocomplete="off"
                            v-model="siswa.name"
                            placeholder="Masukkan Nama"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="name" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            NIS
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="nis"
                            autocomplete="off"
                            v-model="siswa.nis"
                            placeholder="Masukkan NIS"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="nis" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Kelas
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="kelas"
                            autocomplete="off"
                            v-model="siswa.kelas"
                            placeholder="Konfirmasi kelas"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="kelas" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Email
                        </label>
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
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <!-- <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Role
                        </label>
                        <Field
                            name="role_id"
                            type="hidden"
                            v-model="siswa.role_id"
                        >
                            <select2
                                placeholder="Pilih role"
                                class="form-select-solid"
                                :options="roles"
                                name="role_id"
                                v-model="siswa.role_id"
                            >
                            </select2>
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="role_id" />
                            </div>
                        </div>
                    </div> -->
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <!-- <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Nomor Telepon
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="phone"
                            autocomplete="off"
                            v-model="siswa.phone"
                            placeholder="089"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="phone" />
                            </div>
                        </div>
                    </div> -->
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <!-- <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Foto Siswa
                        </label> -->
                        <!--begin::Input-->
                        <!-- <file-upload
                            :files="photo"
                            :accepted-file-types="fileTypes"
                            required
                            v-on:updatefiles="(file) => (photo = file)"
                        ></file-upload> -->
                        <!--end::Input-->
                        <!-- <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="photo" />
                            </div>
                        </div>
                    </div> -->
                    <!--end::Input group-->
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
