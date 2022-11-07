import React from 'react';
import ReactDOM from 'react-dom/client';
import reportWebVitals from './reportWebVitals';
import NavBar from './components/navBar.jsx';
import {
  BrowserRouter as Router,
  Route,
  Link,
  Routes,
  Redirect,
  useLocation
} from "react-router-dom";
import Home from './pages/home.js';
import About from './pages/about.js';
import Historical from './pages/historical.js';
import Test from './pages/test.js';

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(
  <div>
    <Router>
      <NavBar />
      <br/>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/about" element={<About />} />
        <Route path="/historical" element={<Historical />} />
        <Route path="/test" element={<Test />} />
      </Routes>
    </Router>
  </div>
);

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
reportWebVitals();
