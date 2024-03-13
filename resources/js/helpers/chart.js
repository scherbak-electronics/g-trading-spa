import * as LightweightCharts from "lightweight-charts";
import {LineStyle} from "lightweight-charts";


export function getLevelSelectionCrosshair() {
    return {
        mode: LightweightCharts.CrosshairMode.Normal,
        vertLine: {
            width: 1,
            color: '#C3BCDB44',
            style: LightweightCharts.LineStyle.Solid,
            labelBackgroundColor: '#9B7DFF',
        },
        horzLine: {
            width: 1,
            color: '#c2c2c4',
            style: LightweightCharts.LineStyle.LargeDashed
        }
    };
}

export function getDefaultCrosshair() {
    return {
        mode: LightweightCharts.CrosshairMode.Magnet,
        vertLine: {
            width: 1,
            color: '#C3BCDB44',
            style: LightweightCharts.LineStyle.Solid,
            labelBackgroundColor: '#9B7DFF',
        },
        horzLine: {
            width: 1,
            color: '#C3BCDB44',
            style: LightweightCharts.LineStyle.Solid
        }
    };
}

export function getPriceLineOptions(name, price) {
    switch (name) {
        case 'main_level_price':
            return {
                price: price,
                color: '#4d7d9b',
                lineWidth: 1,
                lineStyle: LineStyle.Solid,
                axisLabelVisible: true,
                title: name
            };
        case 'entry_point_price':
            return {
                price: price,
                color: '#44733a',
                lineWidth: 1,
                lineStyle: LineStyle.LargeDashed,
                axisLabelVisible: true,
                title: 'EP'
            };
        case 'take_profit_price':
            return {
                price: price,
                color: '#65b758',
                lineWidth: 1,
                lineStyle: LineStyle.LargeDashed,
                axisLabelVisible: true,
                title: 'TP'
            };
        case 'stop_loss_price':
            return {
                price: price,
                color: '#a85454',
                lineWidth: 1,
                lineStyle: LineStyle.LargeDashed,
                axisLabelVisible: true,
                title: 'SL'
            };
        case 'trailing_delta':
            return {
                price: price,
                color: '#3e8064',
                lineWidth: 1,
                lineStyle: LineStyle.Dashed,
                axisLabelVisible: true,
                title: 'TD'
            };
        default:
            return {
                price: price,
                color: '#757777',
                lineWidth: 1,
                lineStyle: LineStyle.Solid,
                axisLabelVisible: true,
                title: name
            };
    }
}

export function getMinPriceDecimalPlaces(pattern) {
    return pattern.split(".")[1].replace(/0+$/, "").length;
}
