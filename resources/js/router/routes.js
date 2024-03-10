import {default as PageLogin} from "@/views/pages/auth/login/Main";
import {default as PageRegister} from "@/views/pages/auth/register/Main";
import {default as PageResetPassword} from "@/views/pages/auth/reset-password/Main";
import {default as PageForgotPassword} from "@/views/pages/auth/forgot-password/Main";
import {default as PageNotFound} from "@/views/pages/shared/404/Main";
import {default as PageDashboard} from "@/views/pages/private/dashboard/Main";
import {default as PageProfile} from "@/views/pages/private/profile/Main";
import {default as PageUsers} from "@/views/pages/private/users/Index";
import {default as PageUsersCreate} from "@/views/pages/private/users/Create";
import {default as PageUsersEdit} from "@/views/pages/private/users/Edit";
import {default as PageMarkets} from "@/views/pages/private/trading/Markets";
import {default as PageSession} from "@/views/pages/private/trading/Session";
import {default as PageOrders} from "@/views/pages/private/trading/Orders";
import {default as PageDebug} from "@/views/pages/private/trading/Debug";
import {default as PageHomeworkList} from "@/views/pages/private/trading/HomeworkList";
import {default as PageHomeworkCreate} from "@/views/pages/private/trading/HomeworkCreate";
import {default as PageHomeworkEdit} from "@/views/pages/private/trading/HomeworkEdit";


import abilities from "@/stub/abilities";

const routes = [
    {
        name: "home",
        path: "/",
        meta: {requiresAuth: false},
        component: PageLogin,
    },
    {
        name: "panel",
        path: "/panel",
        children: [
            {
                name: "dashboard",
                path: "dashboard",
                meta: {requiresAuth: true, title: 'Dash'},
                component: PageDashboard,
            },
            {
                name: "profile",
                path: "profile",
                meta: {requiresAuth: true, isOwner: true, title: 'Prof'},
                component: PageProfile,
            },
            {
                path: "users",
                children: [
                    {
                        name: "users.list",
                        path: "list",
                        meta: {requiresAuth: true, requiresAbility: abilities.LIST_USER, title: 'Usr'},
                        component: PageUsers,
                    },
                    {
                        name: "users.create",
                        path: "create",
                        meta: {requiresAuth: true, requiresAbility: abilities.CREATE_USER},
                        component: PageUsersCreate,
                    },
                    {
                        name: "users.edit",
                        path: ":id/edit",
                        meta: {requiresAuth: true, requiresAbility: abilities.EDIT_USER},
                        component: PageUsersEdit,
                    },
                ]
            },
        ]
    },
    {
        name: "markets",
        path: "/page/markets",
        meta: {requiresAuth: true, title: 'Markets'},
        component: PageMarkets,
    },
    {
        name: "session",
        path: "/page/session/:id",
        meta: {requiresAuth: true, title: 'Session'},
        component: PageSession,
    },
    {
        name: "homework",
        path: "/page/homework",
        meta: {requiresAuth: true, title: 'Homework', requiresAbility: abilities.LIST_USER},
        component: PageHomeworkList,
    },
    {
        name: "homework_create",
        path: "/page/homework/create",
        meta: {requiresAuth: true, title: 'New Homework', requiresAbility: abilities.LIST_USER},
        component: PageHomeworkCreate,
    },
    {
        name: "homework_edit",
        path: "/page/homework/:id/edit",
        meta: {requiresAuth: true, title: 'Homework Edit', requiresAbility: abilities.EDIT_USER},
        component: PageHomeworkEdit,
    },
    {
        name: "orders",
        path: "/page/orders",
        meta: {requiresAuth: true,  title: 'Orders'},
        component: PageOrders,
    },
    {
        name: "dev",
        path: "/page/dev",
        meta: {requiresAuth: true, title: 'Debug'},
        component: PageDebug,
    },
    {
        path: "/login",
        name: "login",
        meta: {requiresAuth: false},
        component: PageLogin,
    },
    {
        path: "/register",
        name: "register",
        meta: {requiresAuth: false},
        component: PageRegister,
    },
    {
        path: "/reset-password",
        name: "resetPassword",
        meta: {requiresAuth: false},
        component: PageResetPassword,
    },
    {
        path: "/forgot-password",
        name: "forgotPassword",
        meta: {requiresAuth: false},
        component: PageForgotPassword,
    },
    {
        path: "/:catchAll(.*)",
        name: "notFound",
        meta: {requiresAuth: false},
        component: PageNotFound,
    },
]

export default routes;
