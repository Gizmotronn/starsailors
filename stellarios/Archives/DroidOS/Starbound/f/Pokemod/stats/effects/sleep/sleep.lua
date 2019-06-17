function init()
  effect.setParentDirectives("fade=7733AA=0.25")
  effect.addStatModifierGroup({
    {stat = "jumpModifier", amount = -0.3}
  })
end

function update(dt)
  mcontroller.controlModifiers({
      groundMovementModifier = 0.00,
      speedModifier = 0.00,
      airJumpModifier = 0.00
    })
end

function uninit()

end
