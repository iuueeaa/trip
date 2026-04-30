<template>
  <div>
    <a :href="'/' + t.taxonomy + '/' + t.slug" v-for="(t, index) in terms.value" :key="index"><span v-html="t.name"></span></a>
  </div>
</template>

<script setup>
import { ref, defineProps, computed, watch } from "vue";

const props = defineProps({
  terms: {
    type: Object,
    required: true,
  },
});
</script>
