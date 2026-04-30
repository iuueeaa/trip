<script setup>
import { ref, reactive, computed, onMounted, onUpdated } from "vue";

const props = defineProps({
  src: {
    type: Object,
    // default: "_dummy/dummy.webp",
  },
  alt: {
    type: String,
    default: "",
  },
  type: {
    type: String, //img or bgimg
    required: false,
    default: "img",
  },
  size: {
    type: String,
    required: false,
    default: "medium",
  },
});

const imgSrc = computed(() => props.src.sizes[props.size]);
const classes = computed(() => ({ "p-image": true }));
</script>

<template>
  <picture v-if="type == 'img'" :class="classes">
    <img :alt="alt" :src="imgSrc" />
  </picture>
  <div v-else :class="classes">
    <span class="js-lazy_bgi" :data-bgi="imgSrc"></span>
  </div>
</template>

<style lang="scss" scoped></style>
