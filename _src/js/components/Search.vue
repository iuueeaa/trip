<!-- ExampleComponent.vue -->
<template>
  <div></div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
const props = defineProps({
  taxonomy: {
    type: String,
  },
});
const componentData = ref([]);

// コンポーネント内でのAPI取得
// onMounted(async () => {
//   try {
//     const response = await axios.get("https://kidowaki-archit.delaunay.dev/wp/wp-json/wp/v2/works_" + props.taxonomy);
//     componentData.value = response.data; // 新しい配列として再代入
//     console.log(componentData.value); // データ確認
//   } catch (error) {
//     console.error("Error fetching component data:", error);
//   }
// });
</script>
