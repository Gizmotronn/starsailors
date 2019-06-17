function init()
  animator.setParticleEmitterOffsetRegion("drips", mcontroller.boundBox())
  animator.setParticleEmitterActive("drips", true)
  effect.setParentDirectives("fade=300030=0.8")
  effect.addStatModifierGroup({
    {stat = "jumpModifier", amount = -0.50}
  })
end

function update(dt)
  mcontroller.controlModifiers({
      groundMovementModifier = 0.3,
      speedModifier = 0.45,
      airJumpModifier = 0.60
    })
end

function uninit()

end
