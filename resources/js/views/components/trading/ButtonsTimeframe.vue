<template>
    <div class="flex justify-end space-x-2">
        <button class="btn mb-2 mr-2" :class="[classes, { active: selected === '1M' }]" @click="updateValue('1M')">1M</button>
        <button class="btn mb-2 mr-2" :class="[classes, { active: selected === '1d' }]" @click="updateValue('1d')">1d</button>
        <button class="btn mb-2 mr-2" :class="[classes, { active: selected === '1h' }]" @click="updateValue('1h')">1h</button>
        <button class="btn mb-2 mr-2" :class="[classes, { active: selected === '15m' }]" @click="updateValue('15m')">15m</button>
        <button class="btn mb-2 mr-2" :class="[classes, { active: selected === '1m' }]" @click="updateValue('1m')">1m</button>
    </div>
</template>

<script>

export default {
    props: ['modelValue'],
    data() {
        return {
            selected: this.modelValue,
        };
    },
    watch: {
        modelValue(newVal) {
            this.selected = newVal;
        }
    },
    computed: {
        classes() {
            let value = 'px-4 py-2 border border-transparent text-sm rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 text-center transition ';
            value += 'text-white bg-theme-600 hover:bg-theme-800 focus:bg-theme-800 focus:ring-theme-800';
            return value;
        }
    },
    methods: {
        updateValue(val) {
            this.selected = val;
            this.$emit('update:modelValue', val);
        },
    },
};
</script>

<style scoped>
.btn {
    background-color: #b5d2f3;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn.active {
    background-color: #316cbe;
    color: #ffffff;
}

/* flexbox spacing */
.space-x-2 > :not([hidden]) ~ :not([hidden]) {
    --space-x-reverse: 0;
    margin-right: calc(0.5rem * var(--space-x-reverse));
    margin-left: calc(0.5rem * (1 - var(--space-x-reverse)));
}
</style>
