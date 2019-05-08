import React, { Component } from 'react';
import {Image, Menu, Container } from 'semantic-ui-react'

import Validator from "./utils/validationUtils";

import './App.css';

class App extends Component {
    constructor(props) {
        super(props);
        this.state = {
            activeItem: "home"
        }
    }

    componentWillReceiveProps = () => {
        this.handleComponentUpdate();
    }

    componentDidMount = () => {
        this.handleComponentUpdate();
    }

    handleComponentUpdate = () => {
        let item;
        switch (this.props.history.location.pathname) {
            case '/':
                item = 'home'
                break;
            default: 
                item = 'home'
        }
        /* Set active route */
        this.setState({
            activeItem: item
        });
        window.scrollTo(0, 0);
        this.logOutIfInvalid();
    }

    componentDidUpdate = (prevProps) => {
        /* Check for token expiry */
        if (this.props.component.location !== prevProps.component.props.location) {
            this.logOutIfInvalid();
        }
    }

    logOutIfInvalid = () => {
        if (!Validator.tokenIsValid(localStorage.getItem("loginToken"))) {
            localStorage.removeItem("loginToken");
            this.props.history.push('/login');
        }
    }

    /* Log out */
    logOut = () => {
        localStorage.removeItem("loginToken");
        this.props.history.push('/login');
    }

    /* Menu route changes */
    handleRouteChange = (e, { name }) => {
        this.setState({
            activeItem: name
        });
        /* Switch to page */
        switch(e.target.text) {
            case "Home":
                this.props.history.push("/");
                break;
            default:
                this.props.history.push("/");
        }
    }

    render() {
        const { component } = this.props;
        const { activeItem } = this.state;
        return (
            <div className="App">
                <Menu fixed='top' pointing color="orange" stackable>
                    <Container>
                        <Menu.Item as='a' header>
                            Super Awesome System
                        </Menu.Item>
                        <Menu.Item as="a" active={activeItem === "home"} name="home" onClick={this.handleRouteChange} />
                        <Menu.Menu position="right">
                            <Menu.Item as="a" name="logout" icon="power off" onClick={this.logOut}>
                            </Menu.Item>
                        </Menu.Menu>
                    </Container>
                </Menu>
                { component }
            </div>
        );
    }
}

export default App;
