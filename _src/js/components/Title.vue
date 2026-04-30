<script setup>
import { ref, reactive, computed, onMounted, onUpdated } from "vue";
const props = defineProps({
  title: {
    type: Object,
  },
  tag: {
    type: String,
    default: "h2",
  },
});

const selectedTag = ref(null);
selectedTag.value = props.tag;
</script>

<template>
  <div class="title">
    <h2 class="title__wrap">
      <span class="title__main">{{ title.main }}</span>
      <span class="title__sub">{{ title.sub }}</span>
    </h2>
  </div>
</template>

<style scoped></style>
