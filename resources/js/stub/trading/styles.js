const tradingStylesConfig = () => {
    return {
        "bounce": {
            "long": {
                cursorMarker: {
                    position: 'belowBar',
                    color: '#f68410',
                    shape: 'arrowUp',
                    text: 'Click to save'
                },
                levelMarker: {
                    position: 'belowBar',
                    color: '#559955',
                    shape: 'arrowUp',
                    text: 'LFB'
                },
                levelPriceLineOptions: {
                    pricePropertyName: 'low'
                }
            },
            "short": {
                cursorMarker: {
                    position: 'aboveBar',
                    color: '#f68410',
                    shape: 'arrowDown',
                    text: 'Click to save'
                },
                levelMarker: {
                    position: 'aboveBar',
                    color: '#559955',
                    shape: 'arrowDown',
                    text: 'LFB'
                },
                levelPriceLineOptions: {
                    pricePropertyName: 'high'
                }
            }
        },
        "breakout": {
            "long": {
                cursorMarker: {
                    position: 'aboveBar',
                    color: '#44aa55',
                    shape: 'arrowDown',
                    text: 'Click to save'
                },
                levelMarker: {
                    position: 'aboveBar',
                    color: '#559955',
                    shape: 'arrowUp',
                    text: 'LFB'
                },
                levelPriceLineOptions: {
                    pricePropertyName: 'high'
                }
            },
            "short": {
                cursorMarker: {
                    position: 'aboveBar',
                    color: '#44aa55',
                    shape: 'arrowDown',
                    text: 'Click to save'
                },
                levelMarker: {
                    position: 'belowBar',
                    color: '#aa3344',
                    shape: 'arrowDown',
                    text: 'LFB'
                },
                levelPriceLineOptions: {
                    pricePropertyName: 'low'
                }
            }
        },
        "fake_breakout": {
            "long": {
                cursorMarker: {
                    position: 'aboveBar',
                    color: '#44aa55',
                    shape: 'arrowDown',
                    text: 'Click to save'
                },
                levelMarker: {
                    position: 'aboveBar',
                    color: '#005500',
                    shape: 'arrowUp',
                    text: 'LFB'
                },
                levelPriceLineOptions: {
                    pricePropertyName: 'high'
                }
            },
            "short": {
                cursorMarker: {
                    position: 'aboveBar',
                    color: '#550033',
                    shape: 'arrowDown',
                    text: 'Click to save'
                },
                levelMarker: {
                    position: 'aboveBar',
                    color: '#005500',
                    shape: 'arrowDown',
                    text: 'LFB'
                },
                levelPriceLineOptions: {
                    pricePropertyName: 'low'
                }
            }
        }
    };
};

export default tradingStylesConfig;

