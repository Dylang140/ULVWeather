import React, { Component } from 'react';
import './../App.css';
import { fetchWeatherData } from '../fetch/fetchWeatherData';

class Test extends Component {
    state = {
        loading: true,
        value: []
    };

    async componentDidMount() {
        const start = '1665977640';
        const end = '1665977722';
        let data = await fetchWeatherData(start, end);
        if(data != null){
            this.setState({value: data, loading: false});
        }
    }

    getTable() {
        return (
            <table><tbody><tr><th>Time</th><th>Temp</th><th>Wind Speed</th></tr>
            {this.state.value.map((e) => {return <tr><td>{e.time}</td><td>{e.temp}</td><td>{e.windSpeed}</td></tr>})}
            </tbody></table>
        );
    }

    render() {
        return (
            <div id='page'>
                <h1>Test Page</h1>
                <h2>Testing server-side stuff lol</h2>
                <div>
                    {this.state.loading ? <div>loading...</div> : this.getTable()}
                </div>
            </div>
        );
    }
}

export default Test;