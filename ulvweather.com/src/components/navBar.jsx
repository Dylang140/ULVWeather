import React, { Component } from 'react';
import './../App.css';
import { NavLink } from "react-router-dom";

function NavBar() {
    return (
        <div className="navigation">
            <nav className="navbar navbar-expand navbar-dark bg-dark">
                <NavLink to="/" className="navbar-brand">
                    Home
                </NavLink>
                <NavLink to="/about" className="navbar-brand">
                    About
                </NavLink>
                <NavLink to="/historical" className="navbar-brand">
                    History
                </NavLink>
                <NavLink to="/test" className="navbar-brand">
                    Test
                </NavLink>
                <a href="https://api.ulvweather.com" className="navbar-brand">API</a>
                
            </nav>
        </div>  
    );

}

export default NavBar;