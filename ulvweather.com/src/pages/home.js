import React, { Component } from 'react';
import './../App.css';
import { fetchCurrentWeatherData, fetchLastWeatherData } from '../fetch/fetchWeatherData';

class Home extends Component {
    state = {
        loading: true,
        value: []
    };

    async componentDidMount() {
        let data = await fetchCurrentWeatherData();
        this.setState({value: data, loading: false});
        if(data != null){
            this.setState({value: data, loading: false});
        }
    }

    getTable() {
        let uploadDate = new Date(this.state.value['time'] * 1000);
        //<td className="weatherTable">{uploadDate.toDateString() + " " + uploadDate.toTimeString()}</td>
        return (
            <table className="weatherTable">
                <tbody>
                    <tr className="weatherTable">
                        <td>Time Recorded: </td>
                        <td>{uploadDate.toDateString() + " " + uploadDate.toTimeString()}</td>
                    </tr>
                    <tr>
                        <td>Temperature</td>
                        <td>{this.state.value['temp'] * (9/5) + 32} F</td>
                    </tr>
                    <tr>
                        <td>Humidity</td>
                        <td>{this.state.value['humidity']}%</td>
                    </tr>
                    <tr>
                        <td>Pressure</td>
                        <td>{this.state.value['pressure']} hPa</td>
                    </tr>
                    <tr>
                        <td>Wind Speed</td>
                        <td>{this.state.value['windSpeed']} mph</td>
                    </tr>
                    <tr>
                        <td>Wind Direction</td>
                        <td>{this.state.value['windDegree']}, {this.state.value['windDirection']}</td>
                    </tr>
                    <tr>
                        <td>Rain Last Hour</td>
                        <td>{this.state.value['rainSum']}"</td>
                    </tr>
                </tbody>
            </table>
        );
    }

    render() {
        
        return (
            <div id='page'>
                <h1>Home Page</h1>
                <h2>This is the home page</h2>
                <h2>Most Recent Weather Data</h2>
                <div>{this.state.loading ? "Loading..." : this.getTable()}</div>
            </div>
        );
    }
}

export default Home;