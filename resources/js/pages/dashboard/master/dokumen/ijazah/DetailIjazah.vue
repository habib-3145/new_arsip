<script setup>
import { ref, computed } from "vue"; // ← WAJIB agar fotoUrl tidak error

const props = defineProps({
  show: Boolean,
  data: { type: Object, default: () => ({}) }
});

const emit = defineEmits(["close"]);
const close = () => emit("close");

const zoom = ref(false);

// URL gambar otomatis ke /storage/
const fotoUrl = computed(() =>
  props.data?.foto ? `/storage/${props.data.foto}` : "/no-image.png"
);
</script>

<template>
  <!-- ===================== MODAL DETAIL ===================== -->
  <div
    v-if="show"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-[999]"
  >
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-xl relative animate-fadeIn">

      <!-- ❌ Close -->
      <button
        @click="close"
        class="absolute right-3 top-3 text-gray-600 hover:text-black text-xl font-bold"
      >✕</button>

      <h2 class="text-xl font-semibold mb-4">📄 Detail Ijazah Siswa</h2>

      <div class="space-y-2 text-sm leading-relaxed">
        <p><b>Nama:</b> {{ data.nama }}</p>
        <p><b>Kelas:</b> {{ data.kelas }}</p>
        <p><b>Jurusan:</b> {{ data.siswa?.jurusan ?? '-' }}</p>
        <p><b>NISN:</b> {{ data.nisn }}</p>
        <p><b>Tanggal:</b> {{ data.tanggal }}</p>
      </div>

      <!-- Foto -->
      <div class="mt-5">
        <p class="font-semibold mb-2">Foto Ijazah:</p>

        <img
          :src="fotoUrl"
          @click="zoom = true"
          class="w-40 h-52 object-cover rounded shadow cursor-zoom-in
                 hover:scale-105 border transition"
        >
      </div>

    </div>
  </div>

  <!-- ===================== ZOOM FOTO ===================== -->
  <div
    v-if="zoom"
    class="fixed inset-0 bg-black/80 flex items-center justify-center
           z-[1000] animate-fadeIn"
    @click="zoom = false"
  >
    <img
      :src="fotoUrl"
      class="max-w-[85%] max-h-[90%] rounded shadow-2xl cursor-pointer"
    >
  </div>
</template>

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(.95); }
  to   { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
  animation: fadeIn .25s ease-out;
}
</style>
