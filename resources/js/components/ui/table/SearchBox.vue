<template>
  <input
    type="text"
    class="border rounded px-3 py-2 w-full"
    placeholder="Search..."
    v-model="modelValue"
    @input="$emit('update:modelValue', modelValue)"
  />
</template>

<script setup lang="ts">
import { ref, defineProps } from 'vue';
const props = defineProps<{ modelValue: string }>();
const modelValue = ref(props.modelValue);
</script>
