import {defineStore} from 'pinia';
import {toRaw} from 'vue'

export const useLevelsState = defineStore('levels_state', {
    state: () => {
        return {
            levels: []
        }
    },
    actions: {
        addLevel(level) {
            this.levels.push(level);
        },

        getAllLevelsMarkers() {
            let res = [];
            this.levels.forEach(level => {
                res.push(...level.markers);
            });
            return res;
        },

        getLastAddedLevelPrice() {
            if (this.levels.length) {
                return toRaw(this.levels[this.levels.length - 1]).price;
            }
        }
    },
    getters: {
        // priceLineOptions: (state) => {
        //     return state.user ? state.user.is_admin : false;
        // },
        // loggedIn: (state) => {
        //     return !!state.user;
        // },
        // guest: (state) => {
        //     return !state.hasBrowserData();
        // },
    }
});
