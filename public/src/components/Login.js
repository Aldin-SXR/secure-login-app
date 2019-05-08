import React, { Component } from 'react';
import { Button, Form, Grid, Header, Image, Message, Segment, Card, Menu, Container } from 'semantic-ui-react';
import { SemanticToastContainer, toast } from 'react-semantic-toasts';
import 'react-semantic-toasts/styles/react-semantic-alert.css';
import Axios from "axios";
import Validator from "../utils/validationUtils";
import * as Config from "../config";
import ReCAPTCHA from "react-google-recaptcha";

import logo from "../img/slogan.png";

class Login extends Component {
    constructor(props) {
        super(props);
        this.state = {
            email: "",
            password: "",
            error: false,
            loading: false,
            error_message: "",
            captchaResponse: ""
        };
    }

    componentWillMount = () => {
        if (Validator.tokenIsValid(localStorage.getItem("loginToken"))) {
            setTimeout(() => { 
                toast({
                    type: "info",
                    title: "Info",
                    description: <p>You are already logged in.</p>
                });
            }, 100);
            this.props.history.push("/");
        }
    }

    handleSubmit = e => {
        e.preventDefault();
        this.submitForm();
    }

    captchaResponse = (value) => {
        this.setState({
            captchaResponse: value
        });
    }

    handleChange = (e, { name, value }) => {
        this.setState({
            [name]: value
        });
    }

    submitForm = () => {
        /* Check for empty fields */
        if (this.state.email === '' || this.state.password === '') {
            this.setState({
                error: true,
                error_message: "You did not properly fill out login details."
            });
            return;
        }
        /* Send out a request */
        this.setState({
            loading: true
        });
        Axios.post(Config.BASE_URL + "/login", { 
            email_address: this.state.email,
            password: this.state.password,
            captcha_response: this.state.captchaResponse
         }).then(response => {
             this.setState({
                error: false,
                loading: false
             });
             /* Check captcha response */
             if (response.data.response.success) {
                // localStorage.setItem("loginToken", response.data.data.access_token);
                document.querySelector("form").reset();
                setTimeout(() => { 
                    toast({
                        type: "success",
                        title: "Success",
                        description: <p>You have been successfully logged in.</p>
                    });
                }, 100);
                this.props.history.push("/");
             }
         }).catch(error => {
            this.setState({
                error: true,
                error_message: error.response.data.message,
                loading: false
            });
         });
    }

    render() {
        return (
            <div id="login-form" style={{ backgroundColor: "#F6F6F9" }}>
                <style>{`
                    body > div,
                    body > div > div,
                    body > div > div > div.login-form {
                        height: 100%;
                    }
                `}
                </style>
                <SemanticToastContainer position="top-right"/>
                <Menu fixed='top'>
                    <Container>
                        <Menu.Item header>
                            <Image width={50} src={logo} />
                            Some Awesome Project
                        </Menu.Item>
                        <Menu.Item>SSSD project</Menu.Item>
                    </Container>
                </Menu>
                <Grid textAlign='center' style={{ height: '100%' }} verticalAlign='middle'>
                    <Grid.Column style={{ maxWidth: 500 }}>
                        <Card fluid>
                            <Card.Content>
                                <Header as='h2' color='black' textAlign='center'>
                                        <Image src={logo} /> Some Awesome Project
                        </Header>
                                <Form size='large' onSubmit={this.handleSubmit} loading={this.state.loading}>
                                    <Segment>
                                        <Form.Input fluid icon='user' name="email" iconPosition='left' placeholder='E-mail address' onChange={this.handleChange}/>
                                        <Form.Input
                                            fluid
                                            name="password"
                                            icon='lock'
                                            iconPosition='left'
                                            placeholder='Password'
                                            type='password'
                                            onChange={this.handleChange}
                                        />
                                        <ReCAPTCHA
                                            style={{ textAlign: "center", marginLeft: "4em", marginBottom: "1em" }} 
                                            sitekey="6LeMY6IUAAAAAC-Ccy5WQccJFpx7WPZWcvn5rrcK"
                                            onChange={this.captchaResponse}
                                        />
                                        <Button color='orange' fluid size='large'>
                                            Login
                                        </Button>
                                    </Segment>
                                </Form>
                                {
                                    this.state.error &&
                                    <Message negative>
                                        <Message.Header>Could not log you in.</Message.Header>
                                        <p>{ this.state.error_message }</p>
                                    </Message>
                                }
                            </Card.Content>
                        </Card>
                    </Grid.Column>
                </Grid>
            </div>
        )
    }
}

export default Login;