import React, { Component } from 'react'
import { Route, Switch, Router, Redirect } from "react-router-dom";

/* Components */
import App from "../App";
import Home from "../components/Home";
import Login from "../components/Login";

import { createHashHistory } from 'history'
import { SemanticToastContainer } from 'react-semantic-toasts';
const history = createHashHistory();

/** Scroll to top on route change  */
class ScrollToTop extends Component {
    componentDidUpdate = (prevProps) => {
        if (this.props.location.pathname !== prevProps.location.pathname) {
            window.scrollTo(0, 0);
        }
    }

    render() {
        return this.props.children;
    }
}

const PrivateRoute = ({ component: Component, ...rest }) => (
    <Route
        {...rest}
        render={props =>
            localStorage.getItem("loginToken") ? (
                <ScrollToTop location={props.location}>
                    <App history={props.history} component={<Component {...props} />} />
                    <SemanticToastContainer position="top-right"/>
                </ScrollToTop>
            ) : (
                <Redirect
                    to={{
                        pathname: "/login",
                        state: { from: props.location }
                    }}
                />
            )
        }
    />
);

export default () => {
    return (
        <Router history={history} >
            <Switch>
                <PrivateRoute exact path="/" component={Home} />
                <Route exact path="/login" component={Login} />
            </Switch>
        </Router>
    );
}