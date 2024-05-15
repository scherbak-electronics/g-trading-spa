import { defineStore } from 'pinia';

export const useSessionStateStore = defineStore('state_session', {
    state: () => {
        return {
            state: '',
            strategy_code: '',
            total_investment: '',
            total_profit: '',
            main_level_price: '',
            entry_point_price: '',
            take_profit_price: '',
            take_profit_timeout: 0,
            stop_loss_price: '',
            trailing_delta: '',
            stop_loss_safe_time: 0,
            isRunning: false
        }
    },
    actions: {
        setData(data) {
            for (const key in this.$state) {
                if (data.hasOwnProperty(key) && data[key]) {
                    this.$state[key] = data[key];
                    if (key === 'state') {
                        this.isRunning = this.$state[key] === 'running';
                    }
                }
            }
        },
        getLevelsArray() {
            let levels = [];
            if (this.main_level_price) {
                levels.push({
                    name: 'main_level_price',
                    price: this.main_level_price
                });
            }
            if (this.entry_point_price) {
                levels.push({
                    name: 'entry_point_price',
                    price: this.entry_point_price
                });
            }
            if (this.take_profit_price) {
                levels.push({
                    name: 'take_profit_price',
                    price: this.take_profit_price
                });
            }
            if (this.stop_loss_price) {
                levels.push({
                    name: 'stop_loss_price',
                    price: this.stop_loss_price
                });
            }
            return levels;
        }
    }
});
