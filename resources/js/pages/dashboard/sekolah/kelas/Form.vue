<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { Class } from "@/types";
import ApiService from "@/core/services/ApiService";

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const kelas = ref<Class>({} as Class);
const formRef = ref();

const formSchema = Yup.object().shape({
    kelas: Yup.string().required("Nama kelas harus diisi"),
    wali_kelas: Yup.string().required("Wali kelas harus diisi"),
    // jurusan: Yup.string().required("Jurusan harus diisi"),
});

function getEdit() {
    block(document.getElementById("form-class"));
    ApiService.get("master/classes", props.selected)
        .then(({ data }) => {
            kelas.value = data.class;
        })
        .catch((err: any) => {
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-class"));
        });
}

function submit() {
    const formData = new FormData();
    formData.append("kelas", kelas.value.kelas);
    formData.append("wali_kelas", kelas.value.wali_kelas);
    // formData.append("jurusan", kelas.value.jurusan);

    if (props.selected) {
        formData.append("_method", "PUT");
    }

    block(document.getElementById("form-class"));
    axios({
        method: "post",
        url: props.selected
            ? `/classes/${props.selected}`
            : "/classes/data",
        data: formData,
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
        .then(() => {
            emit("close");
            emit("refresh");
            toast.success("Data kelas berhasil disimpan");
            formRef.value.resetForm();
        })
        .catch((err: any) => {
            formRef.value.setErrors(err.response.data.errors);
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-class"));
        });
}

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
        id="form-class"
        ref="formRef"
    >
        <div class="card-header align-items-center">
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Kelas</h2>
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
                <!-- Nama Kelas -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Kelas dan Jurusan
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="kelas"
                            autocomplete="off"
                            v-model="kelas.kelas"
                            placeholder="Masukkan Nama Kelas"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="nama_kelas" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wali Kelas -->
                <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Wali Kelas
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="wali_kelas"
                            autocomplete="off"
                            v-model="kelas.wali_kelas"
                            placeholder="Masukkan Nama Wali Kelas"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="wali_kelas" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jurusan -->
                <!-- <div class="col-md-6">
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Jurusan
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="text"
                            name="jurusan"
                            autocomplete="off"
                            v-model="kelas.jurusan"
                            placeholder="Masukkan Jurusan"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="jurusan" />
                            </div>
                        </div>
                    </div>
                </div> -->

            </div>
        </div>

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-primary btn-sm ms-auto">
                Simpan
            </button>
        </div>
    </VForm>
</template>
