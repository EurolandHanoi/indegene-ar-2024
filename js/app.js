 gsap.registerPlugin(ScrollTrigger)

 // const smoother = ScrollSmoother.create({
 //   content: "#wrapper",
 //   smooth: 3,
 //   effects: true
 // });

 var win = $(this);
 if(win.width()>1025)
 {
  const tl2 = gsap.timeline({ 
    scrollTrigger: {
      trigger: '.hmp-highlights',
      scrub: 2,
    // markers: true,
      toggleActions: 'restart none none none',
      pin: true,
      start: 'top',
      end: '1000'
    }
  });
 
  tl2.to('.hilight-num-inner', { duration: 100, y: -370 });

 }
