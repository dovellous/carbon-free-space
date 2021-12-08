
<style>
:root {
    --theme-color: #e32627;
}
a {
    color: var(--theme-color);
    text-decoration: none;
}
.form-control:focus {
    color: #6e6b7b;
    background-color: #fff;
    border-color: var(--theme-color);
    outline: 0;
    box-shadow: 0 3px 10px 0 rgb(34 41 47 / 10%);
}
.main-menu.menu-dark .navigation > li ul .active {
    background: linear-gradient(
118deg
, #e32627, rgb(240 103 103 / 70%));
    box-shadow: 0 0 10px 1px rgb(240 103 103 / 70%);
    border-radius: 4px;
    z-index: 1;
}

.main-menu .shadow-bottom {
    display: none;
    position: absolute;
    z-index: 2;
    height: 50px;
    width: 100%;
    pointer-events: none;
    margin-top: -0.7rem;
    filter: blur(5px);
    background: linear-gradient(#10163a 41%, rgba(255, 255, 255, 0.11) 95%, rgba(255, 255, 255, 0));
}
.table:not(.table-dark):not(.table-light) thead:not(.table-dark) th, .table:not(.table-dark):not(.table-light) tfoot:not(.table-dark) th {
    background-color: #e32627;
    color: #ffffff;
}

.card-developer-meetup .meetup-img-wrapper {
    background-color: #f44336;
}
</style>
