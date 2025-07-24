class MobileNavBar {

  constructor(mobileMenu, navList, navLinks) {
    this.mobileMenu = document.querySelector(mobileMenu);
    this.navList = document.querySelector(navList);
    this.navLinks = document.querySelectorAll(navLinks);
    this.activeClass = "active";

    this.handleClick = this.handleClick.bind(this);

  }


  animateLinks() {
    this.navLinks.forEach((link, index) => {
      link.style.animation
        ? (link.style.animation = "")
        : (link.style.animation = `navLinkFade 0.5s ease forwards ${index / 7 + 0.3}s`);
    });
  }



  handleClick() {

    this.navList.classList.toggle(this.activeClass);
    this.mobileMenu.classList.toggle(this.activeClass);
    this.animateLinks();
  }




  addClickEvent() {

    this.mobileMenu.addEventListener("click", this.handleClick);

  }

  init() {

    if (this.mobileMenu) {
      this.addClickEvent();
    }
    return this;
  }
}

const mobileNavBar = new MobileNavBar(
  ".mobile-menu",
  ".nave-list",
  ".nave-list li",
  "social-icons",
)

mobileNavBar.init();






const scrollBtn = document.getElementById('scrollToTopBtn');

window.addEventListener('scroll', () => {
  if (window.scrollY > 300) {
    scrollBtn.classList.add('show');
  } else {
    scrollBtn.classList.remove('show');
  }
});

scrollBtn.addEventListener('click', (e) => {
  e.preventDefault();
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
});















